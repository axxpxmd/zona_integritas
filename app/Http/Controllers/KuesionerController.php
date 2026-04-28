<?php

namespace App\Http\Controllers;

use App\Models\{Periode, Komponen, Kategori, SubKategori, Indikator, Pertanyaan, Jawaban};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('page.kuesioner.index', compact('periodes'));
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

                            // Cek apakah pertanyaan ini sudah dijawab
                            $jawaban = $jawabans[$pertanyaan->id] ?? null;

                            if ($jawaban) {
                                $pertanyaanTerjawab++;

                                if ($jawaban->nilai !== null) {
                                    $indPertanyaanTerjawab++;
                                    $indTotalNilai += $jawaban->nilai;
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

        return view('page.kuesioner.pilih-sub-kategori', compact('periode', 'opd', 'komponens', 'progress'));
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

        return view('page.kuesioner.form', compact('periode', 'opd', 'subKategori', 'jawabans', 'currentIndikator', 'currentPage', 'totalIndikator', 'nilaiIndikator'));
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
        ]);

        $periodeId = $request->periode_id;
        $subKategoriId = $request->sub_kategori_id;
        $currentPage = $request->current_page;
        $totalIndikator = $request->total_indikator;
        $pertanyaanIds = $request->pertanyaan_id;
        $jawabanData = $request->jawaban ?? [];
        $keteranganData = $request->keterangan ?? [];
        $fileData = $request->file('file') ?? [];

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
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $file = $fileData[$pertanyaanId] ?? null;

                $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, null, $jawaban, $keterangan, $file);
            }
            // Handle jawaban untuk sub-pertanyaan
            else {
                $nilaiGabungan = null;
                if (isset($jawabanSubData[$pertanyaanId])) {
                    // Hitung relasi acuan <=> capaian
                    $nilaiGabungan = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSubData[$pertanyaanId]);

                    foreach ($jawabanSubData[$pertanyaanId] as $subPertanyaanId => $subJawaban) {
                        // Untuk sub-pertanyaan
                        $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, $subPertanyaanId, $subJawaban, null, null);
                    }
                }

                // Simpan keterangan & file (dan nilai) untuk pertanyaan utamanya (parent)
                $keterangan = $keteranganData[$pertanyaanId] ?? null;
                $file = $fileData[$pertanyaanId] ?? null;

                $existingParent = Jawaban::where('periode_id', $periodeId)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->whereNull('sub_pertanyaan_id')
                    ->first();

                $this->simpanJawaban($periodeId, $opd->id, $pertanyaanId, null, null, $keterangan, $file, $existingParent, $nilaiGabungan);
            }
        }

        // Tetap di halaman form setelah submit
        return redirect()->back()->with('success', 'Jawaban berhasil disimpan');
    }

    /**
     * Helper untuk simpan/update jawaban
     */
    private function simpanJawaban($periodeId, $opdId, $pertanyaanId, $subPertanyaanId, $jawabanValue, $keterangan, $file, $existingJawaban = null, $nilaiGabungan = null)
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

        // Handle file upload
        $filePath = null;
        if ($file && $file->isValid()) {
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = time() . '_' . $pertanyaanId . ($subPertanyaanId ? '_'.$subPertanyaanId : '') . '.' . $ext;
            $storagePath = 'kuesioner/' . $periodeId . '/' . $opdId . '/';

            $file->storeAs($storagePath, $fileName, 'sftp');
            $filePath = $storagePath . $fileName;
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

        // Hanya update file path jika ada file baru yang diupload
        if ($filePath) {
            $data['file_path'] = $filePath;
        }

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
        } else {
            Jawaban::updateOrCreate(
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
            // Jika dalam persen (0-100), konversi ke desimal (0-1)
            $angka = floatval($jawaban);
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

        $idAcuan = $subPertanyaans[0]->id;    // a: penyebut (target/acuan)
        $idRealisasi = $subPertanyaans[1]->id; // b: pembilang (realisasi)

        $nilaiAcuan = floatval($jawabanSubArray[$idAcuan] ?? 0);
        $nilaiRealisasi = floatval($jawabanSubArray[$idRealisasi] ?? 0);

        if ($nilaiAcuan > 0) {
            $capaian = $nilaiRealisasi / $nilaiAcuan;
            return min($capaian, 1.0); // max 100%
        }

        return null;
    }

    /**
     * Hitung nilai indikator berdasarkan formula Excel:
     * Nilai Indikator = AVERAGE(nilai semua pertanyaan) × bobot
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
            $nilai = $jawaban ? $jawaban->nilai : null;

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $jawaban !== null && $jawaban->jawaban_text !== null || $jawaban !== null && $jawaban->jawaban_angka !== null,
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

        $indikator = Indikator::with(['pertanyaans' => function ($q) {
            $q->where('status', 1)->orderBy('urutan');
        }])->findOrFail($request->indikator_id);

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
}
