<?php

namespace App\Http\Controllers;

use App\Models\{Periode, Komponen, Kategori, SubKategori, Indikator, Pertanyaan, Jawaban, JawabanFile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KuesionerController extends Controller
{
    /**
     * Halaman pilih periode
     */
    public function index()
    {
        // Ambil periode yang aktif atau berlangsung
        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $user = Auth::user();
        $opd = $user->opd;

        $totalPertanyaan = Pertanyaan::where('status', 1)->count();

        foreach ($periodes as $periode) {
            if ($opd) {
                $terjawab = Jawaban::where('periode_id', $periode->id)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id')
                    ->count();
                $periode->progress = $totalPertanyaan > 0 ? min(100, round(($terjawab / $totalPertanyaan) * 100)) : 0;
            } else {
                $periode->progress = 0;
            }
        }

        return view('page.kuesioner.index', compact('periodes'));
    }

    /**
     * Halaman Rekapan Hasil Kuesioner (Operator)
     */
    public function rekap($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators' => function ($q) {
                    $q->where('status', 1);
                },
                'kategoris.subKategoris.indikators.pertanyaans' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->orderBy('urutan')
            ->get();

        $progress = [];
        $jawabans = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id')
            ->get()
            ->keyBy('pertanyaan_id');

        $subJawabansAll = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereNotNull('sub_pertanyaan_id')
            ->get()
            ->keyBy(fn ($j) => $j->pertanyaan_id . '-' . $j->sub_pertanyaan_id);

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalNilaiSubKategori = 0;
                    foreach ($subKategori->indikators as $indikator) {
                        $indPertanyaanTerjawab = 0;
                        $indTotalNilai = 0;
                        foreach ($indikator->pertanyaans as $pertanyaan) {
                            $jawaban = $jawabans[$pertanyaan->id] ?? null;
                            if ($jawaban) {
                                $nilaiEfektif = null;
                                $isMenpanDisetujui = $jawaban->status_verifikasi_menpan === 'disetujui';
                                $isVerified = in_array($jawaban->status_verifikasi, ['disetujui', 'terkirim'], true);

                                if ($isVerified && $pertanyaan->has_sub_pertanyaan) {
                                    $subJawabanAngka = [];
                                    foreach ($pertanyaan->subPertanyaans as $sp) {
                                        $spKey = $pertanyaan->id . '-' . $sp->id;
                                        $spJawaban = $subJawabansAll[$spKey] ?? null;
                                        if ($spJawaban) {
                                            $effVal = $isMenpanDisetujui && $spJawaban->menpan_jawaban_angka !== null
                                                ? $spJawaban->menpan_jawaban_angka
                                                : ($spJawaban->verifikator_jawaban_angka ?? $spJawaban->jawaban_angka);

                                            if ($effVal !== null) {
                                                $subJawabanAngka[$sp->id] = $effVal;
                                            }
                                        }
                                    }
                                    $nilaiEfektif = count($subJawabanAngka) >= 2
                                        ? $this->hitungNilaiSubPertanyaan($pertanyaan, $subJawabanAngka)
                                        : $jawaban->nilai;

                                } elseif ($isVerified) {
                                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                                        $effJawaban = $isMenpanDisetujui && $jawaban->menpan_jawaban_text !== null
                                            ? $jawaban->menpan_jawaban_text
                                            : ($jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text);
                                    } else {
                                        $effJawaban = $isMenpanDisetujui && $jawaban->menpan_jawaban_angka !== null
                                            ? $jawaban->menpan_jawaban_angka
                                            : ($jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka);
                                    }
                                    $nilaiEfektif = $effJawaban !== null ? $this->hitungNilai($pertanyaan, $effJawaban) : $jawaban->nilai;
                                } else {
                                    $nilaiEfektif = $jawaban->nilai;
                                }

                                if ($nilaiEfektif !== null) {
                                    $indPertanyaanTerjawab++;
                                    $indTotalNilai += $nilaiEfektif;
                                }
                            }
                        }
                        $indRataRata = $indPertanyaanTerjawab > 0 ? $indTotalNilai / $indPertanyaanTerjawab : 0;
                        $indNilaiAkhir = $indRataRata * $indikator->bobot;
                        $totalNilaiSubKategori += $indNilaiAkhir;
                    }
                    $persenCapaian = $subKategori->bobot > 0 ? ($totalNilaiSubKategori / $subKategori->bobot) * 100 : 0;
                    $progress[$subKategori->id] = [
                        'nilai' => $totalNilaiSubKategori,
                        'capaian' => $persenCapaian
                    ];
                }
            }
        }

        // Siapkan struktur rekapan
        $rekapPengungkit = [];
        $rekapHasil = [];

        foreach ($komponens as $komponen) {
            if ($komponen->nama == 'PENGUNGKIT') {
                foreach ($komponen->kategoris as $kategori) {
                    foreach ($kategori->subKategoris as $subKategori) {
                        $namaArea = trim($subKategori->nama);
                        if (!isset($rekapPengungkit[$namaArea])) {
                            $rekapPengungkit[$namaArea] = [
                                'nama' => $namaArea,
                                'pemenuhan_bobot' => 0,
                                'pemenuhan_nilai' => 0,
                                'reform_bobot' => 0,
                                'reform_nilai' => 0,
                            ];
                        }
                        if (stripos($kategori->nama, 'PEMENUHAN') !== false) {
                            $rekapPengungkit[$namaArea]['pemenuhan_bobot'] = $subKategori->bobot;
                            $rekapPengungkit[$namaArea]['pemenuhan_nilai'] = $progress[$subKategori->id]['nilai'] ?? 0;
                        } else if (stripos($kategori->nama, 'REFORM') !== false) {
                            $rekapPengungkit[$namaArea]['reform_bobot'] = $subKategori->bobot;
                            $rekapPengungkit[$namaArea]['reform_nilai'] = $progress[$subKategori->id]['nilai'] ?? 0;
                        }
                    }
                }
            } else if ($komponen->nama == 'HASIL') {
                foreach ($komponen->kategoris as $kategori) {
                    $subs = [];
                    $nilaiKategori = 0;
                    foreach ($kategori->subKategoris as $subKategori) {
                        $subNilai = $progress[$subKategori->id]['nilai'] ?? 0;
                        $nilaiKategori += $subNilai;
                        $subs[] = [
                            'kode' => $subKategori->kode,
                            'nama' => $subKategori->nama,
                            'bobot' => $subKategori->bobot,
                            'nilai' => $subNilai
                        ];
                    }
                    $rekapHasil[] = [
                        'kode' => $kategori->kode,
                        'nama' => $kategori->nama,
                        'bobot' => $kategori->bobot,
                        'nilai' => $nilaiKategori,
                        'subs' => $subs
                    ];
                }
            }
        }

        return view('page.kuesioner.rekapan', compact('periode', 'opd', 'rekapPengungkit', 'rekapHasil'));
    }

    /**
     * Halaman pilih sub kategori berdasarkan periode
     */
    public function show($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Ambil struktur hierarki: Komponen → Kategori → SubKategori dengan progress
        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris' => function ($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators' => function ($q) {
                    $q->where('status', 1);
                },
                'kategoris.subKategoris.indikators.pertanyaans' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->orderBy('urutan')
            ->get();

        // Hitung progress untuk setiap sub kategori
        $progress = [];
        $jawabans = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id')
            ->get()
            ->keyBy('pertanyaan_id');

        // Preload sub-jawabans untuk perhitungan nilai yang diverifikasi
        $subJawabansAll = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereNotNull('sub_pertanyaan_id')
            ->get()
            ->keyBy(fn ($j) => $j->pertanyaan_id . '-' . $j->sub_pertanyaan_id);

        $totalSemuaPertanyaan = 0;
        $totalPertanyaanTerjawab = 0;

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalPertanyaan = 0;
                    $pertanyaanTerjawab = 0;
                    $totalNilaiSubKategori = 0;

                    foreach ($subKategori->indikators as $indikator) {
                        $indPertanyaanTerjawab = 0;
                        $indTotalNilai = 0;

                        foreach ($indikator->pertanyaans as $pertanyaan) {
                            $totalPertanyaan++;
                            $totalSemuaPertanyaan++;

                            // Cek apakah pertanyaan ini sudah dijawab
                            $jawaban = $jawabans[$pertanyaan->id] ?? null;

                            if ($jawaban) {
                                $pertanyaanTerjawab++;
                                $totalPertanyaanTerjawab++;

                                $nilaiEfektif = null;
                                $isVerified = in_array($jawaban->status_verifikasi, ['disetujui', 'terkirim'], true);

                                if ($isVerified && $pertanyaan->has_sub_pertanyaan) {
                                    // Ambil sub-jawaban dari collection yang sudah di-preload
                                    $subJawabanAngka = [];
                                    foreach ($pertanyaan->subPertanyaans as $sp) {
                                        $spKey = $pertanyaan->id . '-' . $sp->id;
                                        $spJawaban = $subJawabansAll[$spKey] ?? null;
                                        if ($spJawaban) {
                                            $effVal = $spJawaban->verifikator_jawaban_angka ?? $spJawaban->jawaban_angka;
                                            if ($effVal !== null) {
                                                $subJawabanAngka[$sp->id] = $effVal;
                                            }
                                        }
                                    }
                                    $nilaiEfektif = count($subJawabanAngka) >= 2
                                        ? $this->hitungNilaiSubPertanyaan($pertanyaan, $subJawabanAngka)
                                        : $jawaban->nilai;

                                } elseif ($isVerified) {
                                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                                        $effJawaban = $jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text;
                                    } else {
                                        $effJawaban = $jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka;
                                    }
                                    $nilaiEfektif = $effJawaban !== null ? $this->hitungNilai($pertanyaan, $effJawaban) : $jawaban->nilai;
                                } else {
                                    $nilaiEfektif = $jawaban->nilai;
                                }

                                if ($nilaiEfektif !== null) {
                                    $indPertanyaanTerjawab++;
                                    $indTotalNilai += $nilaiEfektif;
                                }
                            }
                        }

                        // Hitung nilai indikator
                        $indRataRata = $indPertanyaanTerjawab > 0 ? $indTotalNilai / $indPertanyaanTerjawab : 0;
                        $indNilaiAkhir = $indRataRata * $indikator->bobot;
                        $totalNilaiSubKategori += $indNilaiAkhir;
                    }

                    // Hitung capaian sub kategori
                    $persenCapaian = $subKategori->bobot > 0 ? ($totalNilaiSubKategori / $subKategori->bobot) * 100 : 0;

                    $progress[$subKategori->id] = [
                        'total' => $totalPertanyaan,
                        'terjawab' => $pertanyaanTerjawab,
                        'persen' => $totalPertanyaan > 0 ? round(($pertanyaanTerjawab / $totalPertanyaan) * 100) : 0,
                        'nilai' => $totalNilaiSubKategori,
                        'capaian' => $persenCapaian
                    ];
                }
            }
        }

        $isAllAnswered = ($totalSemuaPertanyaan > 0 && $totalSemuaPertanyaan === $totalPertanyaanTerjawab);
        $statusFinal = Jawaban::where('periode_id', $periode_id)->where('opd_id', $opd->id)->where('status', 'final')->exists();

        $totalDisetujui = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id')
            ->whereIn('status_verifikasi', ['disetujui', 'terkirim'])
            ->count();
        $totalRevisi = Jawaban::where('periode_id', $periode_id)->where('opd_id', $opd->id)->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'direvisi')->count();
        $totalBelumDiverifikasi = Jawaban::where('periode_id', $periode_id)->where('opd_id', $opd->id)->where('status_verifikasi', 'belum_diverifikasi')->groupBy('sub_pertanyaan_id')->count();
        $totalBelumTerjawab = max(0, $totalSemuaPertanyaan - $totalPertanyaanTerjawab);

        $isSent = $statusFinal || $totalDisetujui > 0 || $totalRevisi > 0 || $totalBelumDiverifikasi > 0;

        return view('page.kuesioner.pilih-sub-kategori', compact(
            'periode',
            'opd',
            'komponens',
            'progress',
            'isAllAnswered',
            'isSent',
            'totalSemuaPertanyaan',
            'totalPertanyaanTerjawab',
            'totalBelumTerjawab',
            'totalDisetujui',
            'totalRevisi',
            'totalBelumDiverifikasi',
            'statusFinal'
        ));
    }

    /**
     * Kirim kuesioner ke verifikator (ubah status menjadi final)
     */
    public function kirimVerifikator(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:tm_periode,id',
        ]);

        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->back()->with('error', 'User belum terhubung dengan OPD');
        }

        // Ubah semua jawaban di periode dan opd ini menjadi final
        Jawaban::where('periode_id', $request->periode_id)
            ->where('opd_id', $opd->id)
            ->update(['status' => 'final']);

        return redirect()->back()->with('success', 'Kuesioner berhasil dikirim ke Verifikator.');
    }

    /**
     * Halaman daftar pertanyaan yang perlu direvisi oleh operator
     */
    public function revisiIndex($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $user    = Auth::user();
        $opd     = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Ambil semua jawaban yang berstatus 'direvisi' milik OPD ini
        $jawabanRevisis = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'direvisi')
            ->whereNull('sub_pertanyaan_id')
            ->with(['files'])
            ->get()
            ->keyBy('pertanyaan_id');

        // Kumpulkan sub-jawabans untuk pertanyaan yang direvisi
        $pertanyaanIds = $jawabanRevisis->keys()->toArray();
        $subJawabansRevisi = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->whereIn('pertanyaan_id', $pertanyaanIds)
            ->whereNotNull('sub_pertanyaan_id')
            ->get()
            ->keyBy(fn ($j) => $j->pertanyaan_id . '-' . $j->sub_pertanyaan_id);

        // Muat struktur pertanyaan yang direvisi, dikelompokkan per sub-kategori → per indikator
        $pertanyaanAllRevisi = Pertanyaan::whereIn('id', $pertanyaanIds)
            ->with([
                'subPertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'indikator.subKategori.kategori.komponen',
            ])
            ->get();

        // Nested groupBy: [sub_kategori_id][indikator_id] → collection of pertanyaan
        $pertanyaanRevisi = $pertanyaanAllRevisi->groupBy([
            fn ($p) => $p->indikator->sub_kategori_id ?? 0,
            fn ($p) => $p->indikator_id ?? 0,
        ]);

        // Kumpulkan objek sub-kategori unik untuk tampilan
        $subKategoris = \App\Models\SubKategori::whereIn('id', $pertanyaanRevisi->keys())
            ->with('kategori.komponen')
            ->get()
            ->keyBy('id');

        // Kumpulkan objek indikator unik untuk tampilan
        $indikatorIds = $pertanyaanAllRevisi->pluck('indikator_id')->unique()->toArray();
        $indikators = \App\Models\Indikator::whereIn('id', $indikatorIds)->get()->keyBy('id');

        $totalRevisi = $jawabanRevisis->count();

        return view('page.kuesioner.revisi', compact(
            'periode',
            'opd',
            'jawabanRevisis',
            'subJawabansRevisi',
            'pertanyaanRevisi',
            'subKategoris',
            'indikators',
            'totalRevisi'
        ));
    }

    /**
     * Simpan jawaban revisi dari operator dan tandai sebagai menunggu dicek ulang
     */
    public function revisiSubmit(Request $request)
    {
        $user = Auth::user();
        $opd  = $user->opd;

        if (!$opd) {
            return redirect()->back()->with('error', 'User belum terhubung dengan OPD');
        }

        $request->validate([
            'periode_id'    => 'required|exists:tm_periode,id',
            'pertanyaan_id' => 'required|array',
            'jawaban'       => 'nullable|array',
            'keterangan'    => 'nullable|array',
            'file'          => 'nullable|array',
            'file.*.*'      => 'file|mimes:pdf|max:5120',
        ]);

        $periodeId      = $request->periode_id;
        $pertanyaanIds  = $request->pertanyaan_id;
        $jawabanData    = $request->jawaban ?? [];
        $jawabanSubData = $request->jawaban_sub ?? [];
        $keteranganData = $request->keterangan ?? [];
        $fileData       = $request->file('file') ?? [];

        foreach ($pertanyaanIds as $pertanyaanId) {
            $pertanyaan = Pertanyaan::find($pertanyaanId);
            if (!$pertanyaan) {
                continue;
            }

            // Cari jawaban yang direvisi
            $existingJawaban = Jawaban::where('periode_id', $periodeId)
                ->where('opd_id', $opd->id)
                ->where('pertanyaan_id', $pertanyaanId)
                ->whereNull('sub_pertanyaan_id')
                ->where('status_verifikasi', 'direvisi')
                ->first();

            if (!$existingJawaban) {
                continue;
            }

            if (!$pertanyaan->has_sub_pertanyaan) {
                $jawaban    = $jawabanData[$pertanyaanId] ?? null;
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $files      = $fileData[$pertanyaanId] ?? [];

                if (!is_array($files)) {
                    $files = [$files];
                }

                // Update jawaban
                if ($jawaban !== null) {
                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                        $existingJawaban->jawaban_text  = $jawaban;
                        $existingJawaban->jawaban_angka = null;
                    } else {
                        $existingJawaban->jawaban_angka = is_numeric($jawaban) ? $jawaban : null;
                        $existingJawaban->jawaban_text  = null;
                    }
                    $existingJawaban->nilai = $this->hitungNilai($pertanyaan, $jawaban);
                }
                if ($keterangan !== null) {
                    $existingJawaban->keterangan = $keterangan;
                }
            } else {
                // Sub-pertanyaan
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $files      = $fileData[$pertanyaanId] ?? [];
                if (!is_array($files)) {
                    $files = [$files];
                }

                if (isset($jawabanSubData[$pertanyaanId])) {
                    $nilaiGabungan = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSubData[$pertanyaanId]);
                    foreach ($jawabanSubData[$pertanyaanId] as $subId => $subVal) {
                        $subJawaban = Jawaban::where('periode_id', $periodeId)
                            ->where('opd_id', $opd->id)
                            ->where('pertanyaan_id', $pertanyaanId)
                            ->where('sub_pertanyaan_id', $subId)
                            ->first();
                        if ($subJawaban) {
                            $subJawaban->jawaban_angka        = is_numeric($subVal) ? $subVal : null;
                            $subJawaban->menunggu_dicek_ulang = true;
                            $subJawaban->revised_at           = now();
                            $subJawaban->revised_by           = $user->id;
                            $subJawaban->revisi_count         = ($subJawaban->revisi_count ?? 0) + 1;
                            $subJawaban->status_verifikasi    = 'belum_diverifikasi';
                            $subJawaban->status_verifikasi_menpan = 'belum_diverifikasi';
                            $subJawaban->menpan_jawaban_text = null;
                            $subJawaban->menpan_jawaban_angka = null;
                            $subJawaban->menpan_verified_by = null;
                            $subJawaban->menpan_verified_at = null;
                            $subJawaban->updated_by           = $user->id;
                            $subJawaban->save();
                        }
                    }
                    $existingJawaban->nilai = $nilaiGabungan;
                }
                if ($keterangan !== null) {
                    $existingJawaban->keterangan = $keterangan;
                }
            }

            // Tandai sudah direvisi operator → menunggu dicek ulang verifikator
            $existingJawaban->status_verifikasi    = 'belum_diverifikasi';
            $existingJawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $existingJawaban->menunggu_dicek_ulang = true;
            $existingJawaban->revised_at           = now();
            $existingJawaban->revised_by           = $user->id;
            $existingJawaban->revisi_count         = ($existingJawaban->revisi_count ?? 0) + 1;
            $existingJawaban->menpan_jawaban_text  = null;
            $existingJawaban->menpan_jawaban_angka = null;
            $existingJawaban->menpan_verified_by   = null;
            $existingJawaban->menpan_verified_at   = null;
            $existingJawaban->updated_by           = $user->id;
            $existingJawaban->save();

            // Simpan file jika ada
            $this->simpanFileJawaban(
                $existingJawaban,
                array_filter($files),
                $periodeId,
                $opd->id,
                $pertanyaanId,
                null,
                $existingJawaban->revisi_count
            );
        }

        return redirect()->route('kuesioner.revisi.index', $periodeId)
            ->with('success', 'Revisi berhasil dikirim. Verifikator akan memeriksa kembali jawaban Anda.');
    }

    /**
     * Halaman form isi kuesioner per sub kategori
     */
    public function fill(Request $request, $periode_id, $sub_kategori_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $subKategori = SubKategori::with([
            'kategori.komponen',
            'indikators' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            },
            'indikators.pertanyaans' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            },
            'indikators.pertanyaans.subPertanyaans' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            }
        ])->findOrFail($sub_kategori_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Pagination indikator
        $indikators = $subKategori->indikators;
        $totalIndikator = $indikators->count();

        if ($totalIndikator == 0) {
            return redirect()->route('kuesioner.show', $periode_id)
                ->with('error', 'Belum ada indikator/pertanyaan untuk sub-kategori ini.');
        }

        $currentPage = (int) max(1, min($request->get('indikator', 1), $totalIndikator));
        $currentIndikator = $indikators->get($currentPage - 1);

        if (!$currentIndikator) {
            return redirect()->route('kuesioner.fill', [$periode_id, $sub_kategori_id, 'indikator' => 1]);
        }

        // Ambil jawaban yang sudah diisi oleh OPD ini untuk periode ini
        $jawabans = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->with('files')
            ->get()
            ->keyBy(function ($item) {
                // Key: pertanyaan_id atau pertanyaan_id-sub_pertanyaan_id
                return $item->sub_pertanyaan_id
                    ? $item->pertanyaan_id . '-' . $item->sub_pertanyaan_id
                    : $item->pertanyaan_id;
            });

        // Hitung nilai per indikator berdasarkan formula Excel:
        // Nilai Indikator = AVERAGE(nilai semua pertanyaan) × bobot
        $nilaiIndikator = $this->hitungNilaiIndikator($currentIndikator, $jawabans);

        $isSent = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->where('status', 'final')
            ->exists();

        return view('page.kuesioner.form', compact('periode', 'opd', 'subKategori', 'jawabans', 'currentIndikator', 'currentPage', 'totalIndikator', 'nilaiIndikator', 'isSent'));
    }

    /**
     * Simpan jawaban kuesioner
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->back()->with('error', 'User belum terhubung dengan OPD');
        }

        $request->validate([
            'periode_id' => 'required|exists:tm_periode,id',
            'sub_kategori_id' => 'required|exists:tm_sub_kategori,id',
            'indikator_id' => 'required|exists:tm_indikator,id',
            'pertanyaan_id' => 'required|array',
            'jawaban' => 'nullable|array',
            'keterangan' => 'nullable|array',
            'file' => 'nullable|array',
            'file.*' => 'nullable|array',
            'file.*.*' => 'file|mimes:pdf|max:5120',
        ]);

        $periodeId = $request->periode_id;
        $subKategoriId = $request->sub_kategori_id;
        $currentPage = $request->current_page;
        $totalIndikator = $request->total_indikator;
        $pertanyaanIds = $request->pertanyaan_id;
        $jawabanData = $request->jawaban ?? [];
        $jawabanSubData = $request->jawaban_sub ?? [];
        $keteranganData = $request->keterangan ?? [];
        $fileData = $request->file('file') ?? [];

        foreach ($pertanyaanIds as $pertanyaanId) {
            $pertanyaan = Pertanyaan::find($pertanyaanId);
            if (!$pertanyaan) {
                continue;
            }

            // Handle jawaban untuk pertanyaan utama (non-sub)
            if (!$pertanyaan->has_sub_pertanyaan) {
                $jawaban = $jawabanData[$pertanyaanId] ?? null;
                if ($jawaban === null || $jawaban === '') {
                    continue;
                }
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $files = $fileData[$pertanyaanId] ?? [];

                if ($files instanceof \Illuminate\Http\UploadedFile) {
                    $files = [$files];
                }

                if (!is_array($files)) {
                    $files = [];
                }

                $jawabanModel = $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, null, $jawaban, $keterangan);
                $this->simpanFileJawaban($jawabanModel, $files, $periodeId, $opd->id, $pertanyaanId);
            }
            // Handle jawaban untuk sub-pertanyaan
            else {
                $nilaiGabungan = null;
                $jawabanSubRaw = $jawabanSubData[$pertanyaanId] ?? [];
                if (!is_array($jawabanSubRaw)) {
                    $jawabanSubRaw = [];
                }
                $jawabanSub = array_filter($jawabanSubRaw, function ($val) {
                    return $val !== null && $val !== '';
                });

                if (empty($jawabanSub)) {
                    continue;
                }

                // Hitung relasi acuan <=> capaian
                $nilaiGabungan = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSub);

                foreach ($jawabanSub as $subPertanyaanId => $subJawaban) {
                    // Untuk sub-pertanyaan
                    $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, $subPertanyaanId, $subJawaban, null, null);
                }

                // Simpan keterangan & file (dan nilai) untuk pertanyaan utamanya (parent)
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $files = $fileData[$pertanyaanId] ?? [];

                if ($files instanceof \Illuminate\Http\UploadedFile) {
                    $files = [$files];
                }

                if (!is_array($files)) {
                    $files = [];
                }

                $existingParent = Jawaban::where('periode_id', $periodeId)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->whereNull('sub_pertanyaan_id')
                    ->first();

                $jawabanModel = $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, null, null, $keterangan, $existingParent, $nilaiGabungan);
                $this->simpanFileJawaban($jawabanModel, $files, $periodeId, $opd->id, $pertanyaanId);
            }
        }

        // Tetap di halaman form setelah submit
        return redirect()->back()->with('success', 'Jawaban berhasil disimpan');
    }

    /**
     * Helper untuk simpan/update jawaban
     */
    private function simpanJawaban($periodeId, $opdId, $pertanyaanId, $subPertanyaanId, $jawabanValue, $keterangan, $existingJawaban = null, $nilaiGabungan = null)
    {
        $user = Auth::user();
        $pertanyaan = Pertanyaan::find($pertanyaanId);

        // Tentukan field yang akan disimpan
        $jawabanText = null;
        $jawabanAngka = null;

        // Default nilai null
        $nilai = $nilaiGabungan;

        if ($jawabanValue !== null) {
            if ($subPertanyaanId) {
                // Sub-pertanyaan selalu angka
                $jawabanAngka = is_numeric($jawabanValue) ? $jawabanValue : null;
            } else {
                if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                    $jawabanText = $jawabanValue;
                } else {
                    $jawabanAngka = is_numeric($jawabanValue) ? $jawabanValue : null;
                }
            }
            // Hitung nilai hanya untuk jawaban utama yang bukan gabungan
            if (!$subPertanyaanId && $nilaiGabungan === null) {
                $nilai = $this->hitungNilai($pertanyaan, $jawabanValue);
            }
        }

        $data = [
            'jawaban_text' => $jawabanText,
            'jawaban_angka' => $jawabanAngka,
            'nilai' => $nilai,
            'keterangan' => $keterangan,
            'status' => 'draft',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];

        // Jika ada existing jawaban (untuk update keterangan/file di parent), gunakan itu
        if ($existingJawaban) {
            // Jangan null-kan field jawaban jika hanya update keterangan/file
            if ($jawabanValue === null) {
                unset($data['jawaban_text']);
                unset($data['jawaban_angka']);

                // Jika tidak ada nilai gabungan yang dikirim (bukan parent update nilai)
                if ($nilaiGabungan === null && !$subPertanyaanId) {
                    unset($data['nilai']);
                }
            }
            if (!$keterangan) {
                unset($data['keterangan']);
            }

            // Jika nilai sengaja diset (misal update nilai gabungan val 0)
            if ($nilaiGabungan !== null) {
                $data['nilai'] = $nilaiGabungan;
            }

            $existingJawaban->update(array_filter($data, function ($val, $key) {
                // Biarkan 'nilai' bisa divaluesi 0 (null di filter jika di set)
                return $val !== null || $key === 'nilai';
            }, ARRAY_FILTER_USE_BOTH));
            return $existingJawaban;
        } else {
            return Jawaban::updateOrCreate(
                [
                    'periode_id' => $periodeId,
                    'opd_id' => $opdId,
                    'pertanyaan_id' => $pertanyaanId,
                    'sub_pertanyaan_id' => $subPertanyaanId,
                ],
                $data
            );
        }
    }

    /**
     * Simpan file jawaban (multi-file)
     */
    private function simpanFileJawaban(
        Jawaban $jawaban,
        array $files,
        $periodeId,
        $opdId,
        $pertanyaanId,
        $subPertanyaanId = null,
        $revisiKe = null
    ): void
    {
        if (empty($files)) {
            return;
        }

        $user = Auth::user();
        $storagePath = 'kuesioner/' . $periodeId . '/' . $opdId . '/';
        $lastFilePath = null;

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = time() . '_' . $pertanyaanId . ($subPertanyaanId ? '_' . $subPertanyaanId : '') . '_' . Str::random(6) . '.' . $ext;

            $file->storeAs($storagePath, $fileName, 'sftp');
            $filePath = $storagePath . $fileName;

            JawabanFile::create([
                'jawaban_id' => $jawaban->id,
                'revisi_ke' => $revisiKe,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $user ? $user->id : null,
            ]);

            $lastFilePath = $filePath;
        }

        if ($lastFilePath) {
            $jawaban->update(['file_path' => $lastFilePath]);
        }
    }

    /**
     * Hitung nilai berdasarkan tipe jawaban
     * Formula berdasarkan Excel form kuesioner:
     * - Ya/Tidak: Ya = 1, Tidak = 0
     * - A/B/C: A = 1, B = 0.5, C = 0
     * - A/B/C/D: A = 1, B = 0.67, C = 0.33, D = 0
     * - A/B/C/D/E: A = 1, B = 0.75, C = 0.5, D = 0.25, E = 0
     * - Angka/%: Nilai langsung (persentase atau angka)
     */
    private function hitungNilai(Pertanyaan $pertanyaan, $jawaban): ?float
    {
        if (empty($jawaban)) {
            return null;
        }

        $tipe = $pertanyaan->tipe_jawaban;

        // Untuk tipe Ya/Tidak
        if ($tipe === 'ya_tidak') {
            return strtolower($jawaban) === 'ya' ? 1.0 : 0.0;
        }

        // Untuk tipe Pilihan Ganda (A/B/C, A/B/C/D, A/B/C/D/E)
        if ($tipe === 'pilihan_ganda') {
            $jumlahOpsi = count($pertanyaan->penjelasan_list);
            $opsi = strtoupper($jawaban);

            // Scoring berdasarkan jumlah opsi
            $skorMap = $this->getSkorMap($jumlahOpsi);

            return $skorMap[$opsi] ?? null;
        }

        // Untuk tipe Angka (persentase, jumlah, dll)
        if ($tipe === 'angka') {
            $angka = floatval($jawaban);

            // Khusus pertanyaan survei eksternal tertentu, nilai maksimalnya adalah 4
            if (
                str_contains($pertanyaan->pertanyaan, 'Nilai Survey Persepsi Korupsi (Survei Eksternal)') ||
                str_contains($pertanyaan->pertanyaan, 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
            ) {
                return $angka / 4;
            }

            // Jika dalam persen (0-100), konversi ke desimal (0-1)
            if ($angka > 1 && $angka <= 100) {
                return $angka / 100;
            }
            return $angka;
        }

        return null;
    }

    /**
     * Get skor mapping berdasarkan jumlah opsi
     * Sesuai dengan formula Excel
     */
    private function getSkorMap(int $jumlahOpsi): array
    {
        switch ($jumlahOpsi) {
            case 2: // Ya/Tidak atau A/B
                return [
                    'A' => 1.0,
                    'B' => 0.0,
                ];
            case 3: // A/B/C
                return [
                    'A' => 1.0,
                    'B' => 0.5,
                    'C' => 0.0,
                ];
            case 4: // A/B/C/D
                return [
                    'A' => 1.0,
                    'B' => 0.67,
                    'C' => 0.33,
                    'D' => 0.0,
                ];
            case 5: // A/B/C/D/E
                return [
                    'A' => 1.0,
                    'B' => 0.75,
                    'C' => 0.5,
                    'D' => 0.25,
                    'E' => 0.0,
                ];
            default:
                // Fallback: distribusi linear
                $map = [];
                $letters = range('A', chr(64 + $jumlahOpsi));
                foreach ($letters as $index => $letter) {
                    $map[$letter] = round(1 - ($index / ($jumlahOpsi - 1)), 2);
                }
                return $map;
        }
    }

    /**
     * Hitung nilai pertanyaan yang memiliki sub-pertanyaan
     * Misalnya: a = target (penyebut), b = realisasi (pembilang)
     * Nilai = b / a
     */
    private function hitungNilaiSubPertanyaan(Pertanyaan $pertanyaan, array $jawabanSubArray): ?float
    {
        if (count($jawabanSubArray) < 2) {
            return null; // butuh minimal 2 nilai (acuan dan realisasi)
        }

        $subPertanyaans = $pertanyaan->subPertanyaans()->orderBy('urutan')->get();
        if ($subPertanyaans->count() < 2) {
            return null;
        }

        $idAcuan = $subPertanyaans->first()->id;    // default: a: penyebut (target/acuan)
        $idRealisasi = $subPertanyaans->last()->id; // default: b: pembilang (realisasi)

        // Penyesuaian id pembilang/penyebut untuk pertanyaan spesifik
        if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
            // b: pembilang ada di urutan 2 (index 1)
            $idRealisasi = $subPertanyaans->get(1)->id;
        } elseif (str_contains($pertanyaan->pertanyaan, 'Persentase penyampaian LHKPN')) {
            // b: pembilang ada di urutan 5 (index 4) atau yang terakhir
            // urutan 1 = Jumlah yang harus melaporkan, urutan 5 = Jumlah yang sudah melaporkan
            $idRealisasi = $subPertanyaans->where('urutan', 5)->first()->id ?? $subPertanyaans->last()->id;
        }

        $nilaiAcuan = floatval($jawabanSubArray[$idAcuan] ?? 0);
        $nilaiRealisasi = floatval($jawabanSubArray[$idRealisasi] ?? 0);

        if ($nilaiAcuan > 0) {
            // Khusus untuk Penurunan pelanggaran disiplin pegawai
            if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
                // Rumus: (Tahun Lalu - Tahun Ini) / Tahun Lalu
                // misal: (10 - 5) / 10 = 50%, (10 - 7) / 10 = 30%
                $capaian = ($nilaiAcuan - $nilaiRealisasi) / $nilaiAcuan;
                // Pastikan tidak negatif
                return max(0, min($capaian, 1.0));
            }

            $capaian = $nilaiRealisasi / $nilaiAcuan;
            return min($capaian, 1.0); // max 100%
        }

        return null;
    }

    /**
     * Hitung nilai indikator berdasarkan formula Excel:
     * Nilai Indikator = AVERAGE(nilai semua pertanyaan) × bobot
     * Jika pertanyaan sudah diverifikasi, gunakan jawaban verifikator untuk menghitung nilai.
     */
    private function hitungNilaiIndikator(Indikator $indikator, $jawabans): array
    {
        $pertanyaans = $indikator->pertanyaans;
        $totalPertanyaan = $pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($pertanyaans as $pertanyaan) {
            $jawaban = $jawabans[$pertanyaan->id] ?? null;
            $nilai = null;

            if ($jawaban) {
                $isVerified = in_array($jawaban->status_verifikasi, ['disetujui', 'terkirim'], true);

                if ($isVerified && $pertanyaan->has_sub_pertanyaan) {
                    // Kumpulkan nilai sub dari jawaban verifikator
                    $subJawabanAngka = [];
                    foreach ($pertanyaan->subPertanyaans as $sp) {
                        $spKey = $pertanyaan->id . '-' . $sp->id;
                        $spJawaban = $jawabans[$spKey] ?? null;
                        if ($spJawaban) {
                            $effVal = $spJawaban->verifikator_jawaban_angka ?? $spJawaban->jawaban_angka;
                            if ($effVal !== null) {
                                $subJawabanAngka[$sp->id] = $effVal;
                            }
                        }
                    }
                    $nilai = count($subJawabanAngka) >= 2
                        ? $this->hitungNilaiSubPertanyaan($pertanyaan, $subJawabanAngka)
                        : $jawaban->nilai;

                } elseif ($isVerified) {
                    // Gunakan jawaban verifikator (fallback ke operator jika null)
                    $effJawaban = null;
                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                        $effJawaban = $jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text;
                    } else {
                        $effJawaban = $jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka;
                    }
                    $nilai = $effJawaban !== null ? $this->hitungNilai($pertanyaan, $effJawaban) : $jawaban->nilai;

                } else {
                    // Belum diverifikasi: gunakan nilai operator dari DB
                    $nilai = $jawaban->nilai;
                }
            }

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $jawaban !== null && ($jawaban->jawaban_text !== null || $jawaban->jawaban_angka !== null),
            ];

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $pertanyaanTerjawab++;
            }
        }

        // Hitung rata-rata nilai pertanyaan
        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;

        // Nilai indikator = rata-rata × bobot
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;

        // Persentase capaian = nilai indikator / bobot × 100
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return [
            'total_pertanyaan' => $totalPertanyaan,
            'pertanyaan_terjawab' => $pertanyaanTerjawab,
            'rata_rata_nilai' => round($rataRataNilai, 2),
            'bobot' => $indikator->bobot,
            'nilai_indikator' => round($nilaiIndikator, 2),
            'persen_capaian' => round($persenCapaian, 2),
            'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
        ];
    }

    /**
     * Hitung nilai preview (AJAX) - untuk update real-time tanpa save
     */
    public function hitungNilaiPreview(Request $request)
    {
        $request->validate([
            'indikator_id' => 'required|exists:tm_indikator,id',
            'jawaban' => 'nullable|array',
            'jawaban_sub' => 'nullable|array',
        ]);

        $indikator = Indikator::with([
            'pertanyaans' => function ($q) {
                $q->where('status', 1)->orderBy('urutan');
            }
        ])->findOrFail($request->indikator_id);

        $jawabanInput = $request->jawaban ?? [];
        $jawabanSubInput = $request->jawaban_sub ?? [];
        $totalPertanyaan = $indikator->pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($indikator->pertanyaans as $pertanyaan) {
            $nilai = null;
            $terjawab = false;

            if ($pertanyaan->has_sub_pertanyaan) {
                // Untuk pertanyaan dengan sub, cek array jawaban sub-nya
                $jawabanSub = $jawabanSubInput[$pertanyaan->id] ?? null;
                if (!empty($jawabanSub) && is_array($jawabanSub) && count($jawabanSub) >= 2) {
                    $nilai = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSub);
                    $terjawab = true;
                }
            } else {
                // Untuk pertanyaan biasa
                $jawaban = $jawabanInput[$pertanyaan->id] ?? null;
                if (!empty($jawaban)) {
                    $nilai = $this->hitungNilai($pertanyaan, $jawaban);
                    $terjawab = true;
                }
            }

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $pertanyaanTerjawab++;
            }

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $terjawab,
            ];
        }

        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_pertanyaan' => $totalPertanyaan,
                'pertanyaan_terjawab' => $pertanyaanTerjawab,
                'rata_rata_nilai' => round($rataRataNilai, 2),
                'bobot' => $indikator->bobot,
                'nilai_indikator' => round($nilaiIndikator, 2),
                'persen_capaian' => round($persenCapaian, 2),
                'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
            ],
        ]);
    }

    /**
     * Tampilkan file dokumen kuesioner yang sudah diunggah
     */
    public function viewFile($id)
    {
        $jawaban = Jawaban::findOrFail($id);

        if (!$jawaban->file_path || !Storage::disk('sftp')->exists($jawaban->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $user = Auth::user();
        // Hanya verifikator, admin, atau OPD yang memiliki jawaban ini yang boleh mengakses
        if ($user->role === 'operator' && $jawaban->opd_id !== $user->opd_id) {
            abort(403, 'Akses ditolak.');
        }

        $fileContent = Storage::disk('sftp')->get($jawaban->file_path);
        $mimeType = Storage::disk('sftp')->mimeType($jawaban->file_path);

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($jawaban->file_path) . '"'
        ]);
    }

    /**
     * Tampilkan file dokumen jawaban (multi-file)
     */
    public function viewFileItem($id)
    {
        $file = JawabanFile::with('jawaban')->findOrFail($id);
        $jawaban = $file->jawaban;

        if (!$file->file_path || !Storage::disk('sftp')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $user = Auth::user();
        // Hanya verifikator, admin, atau OPD yang memiliki jawaban ini yang boleh mengakses
        if ($user->role === 'operator' && $jawaban->opd_id !== $user->opd_id) {
            abort(403, 'Akses ditolak.');
        }

        $fileContent = Storage::disk('sftp')->get($file->file_path);
        $mimeType = Storage::disk('sftp')->mimeType($file->file_path);

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($file->original_name ?: basename($file->file_path)) . '"'
        ]);
    }

    /**
     * Hapus file dokumen kuesioner
     */
    public function deleteFile($id)
    {
        $file = JawabanFile::with('jawaban')->findOrFail($id);
        $jawaban = $file->jawaban;

        $user = Auth::user();
        // Hanya pemilik (operator OPD) atau admin yang bisa menghapus
        if ($user->role === 'operator' && $jawaban->opd_id !== $user->opd_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        try {
            // Hapus dari storage
            if (Storage::disk('sftp')->exists($file->file_path)) {
                Storage::disk('sftp')->delete($file->file_path);
            }

            // Hapus dari database
            $file->delete();

            // Jika jawaban.file_path sama dengan file yang dihapus, update ke file lain atau null
            if ($jawaban->file_path === $file->file_path) {
                $lastFile = $jawaban->files()->latest()->first();
                $jawaban->update(['file_path' => $lastFile ? $lastFile->file_path : null]);
            }

            return response()->json(['success' => true, 'message' => 'File berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus file: ' . $e->getMessage()], 500);
        }
    }
}
