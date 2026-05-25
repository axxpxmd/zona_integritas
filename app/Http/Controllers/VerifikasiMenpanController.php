<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Opd;
use App\Models\Komponen;
use App\Models\SubKategori;
use App\Models\Periode;
use App\Models\Indikator;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiMenpanController extends Controller
{
    private function authorizeMenpan(): void
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator_menpan'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeMenpan();

        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $periodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        $activePeriode = $periodeId ? Periode::find($periodeId) : null;

        $submittedOpds = collect();

        if ($activePeriode) {
            $opdIds = Jawaban::where('periode_id', $activePeriode->id)
                ->where('status', 'final')
                ->whereNull('sub_pertanyaan_id')
                ->distinct()
                ->pluck('opd_id');

            $opds = Opd::whereIn('id', $opdIds)->get();

            foreach ($opds as $opd) {
                $opd_base = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $opd->total_jawaban = (clone $opd_base)->count();
                $opd->terverifikasi_verifikator = (clone $opd_base)->where('status_verifikasi', 'terkirim')->count();

                if ($opd->total_jawaban === 0 || $opd->terverifikasi_verifikator < $opd->total_jawaban) {
                    continue;
                }

                $lastSubmit = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->where('status', 'final')
                    ->max('updated_at');

                $opd->submitted_at = $lastSubmit;

                $opd->terverifikasi = (clone $opd_base)->where('status_verifikasi_menpan', 'disetujui')->count();
                $opd->belum_terverifikasi = (clone $opd_base)->where('status_verifikasi_menpan', 'belum_diverifikasi')->count();
                $opd->persen = $opd->total_jawaban > 0
                    ? min(100, round(($opd->terverifikasi / $opd->total_jawaban) * 100))
                    : 0;

                $submittedOpds->push($opd);
            }
        }

        return view('page.verifikasi-menpan.index', compact('periodes', 'activePeriode', 'submittedOpds'));
    }

    public function show(Periode $periode, Opd $opd)
    {
        $this->authorizeMenpan();

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');
        $totalJawaban = (clone $opdBase)->count();
        $totalDisetujui = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();

        if ($totalJawaban === 0 || $totalDisetujui < $totalJawaban) {
            return redirect()->route('verifikasi-menpan.index')
                ->with('error', 'OPD belum siap diverifikasi Menpan.');
        }

        $komponens = Komponen::with(['kategoris.subKategoris.indikators.pertanyaans.subPertanyaans'])
            ->orderBy('urutan')
            ->get();

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'terkirim')
            ->with('files')
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $verifikasiStats = [
            'total_jawaban' => $jawabans->count(),
            'belum_diverifikasi' => $jawabans->where('status_verifikasi_menpan', 'belum_diverifikasi')->count(),
            'disetujui' => $jawabans->where('status_verifikasi_menpan', 'disetujui')->count(),
        ];

        $jawabansParent = $jawabans->whereNull('sub_pertanyaan_id')->keyBy('pertanyaan_id');
        $progress = [];
        $totalSemuaPertanyaan = 0;
        $totalPertanyaanTerjawab = 0;
        $totalPertanyaanTerverifikasi = 0;

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalPertanyaan = 0;
                    $pertanyaanTerjawab = 0;
                    $pertanyaanTerverifikasi = 0;
                    $totalNilaiSubKategori = 0;

                    foreach ($subKategori->indikators as $indikator) {
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorMenpan($indikator, $jawabanMap);

                        $totalPertanyaan += $nilaiIndikatorData['total_pertanyaan'];
                        $totalSemuaPertanyaan += $nilaiIndikatorData['total_pertanyaan'];

                        $pertanyaanTerjawab += $nilaiIndikatorData['pertanyaan_terjawab'];
                        $totalPertanyaanTerjawab += $nilaiIndikatorData['pertanyaan_terjawab'];

                        $pertanyaanTerverifikasi += $nilaiIndikatorData['pertanyaan_terverifikasi'];
                        $totalPertanyaanTerverifikasi += $nilaiIndikatorData['pertanyaan_terverifikasi'];

                        $totalNilaiSubKategori += $nilaiIndikatorData['nilai_indikator'];
                    }

                    $persenCapaian = $subKategori->bobot > 0 ? ($totalNilaiSubKategori / $subKategori->bobot) * 100 : 0;

                    $progress[$subKategori->id] = [
                        'total' => $totalPertanyaan,
                        'terverifikasi' => $pertanyaanTerverifikasi,
                        'persen' => $totalPertanyaan > 0 ? round(($pertanyaanTerverifikasi / $totalPertanyaan) * 100) : 0,
                        'nilai' => $totalNilaiSubKategori,
                        'capaian' => $persenCapaian,
                    ];
                }
            }
        }

        $verifikasiStats['total_pertanyaan'] = $totalSemuaPertanyaan;
        $verifikasiStats['terverifikasi'] = $totalPertanyaanTerverifikasi;
        $verifikasiStats['belum_terverifikasi'] = max(0, $totalSemuaPertanyaan - $totalPertanyaanTerverifikasi);

        $isAllAnswered = ($totalSemuaPertanyaan > 0 && $totalSemuaPertanyaan === $totalPertanyaanTerjawab);
        $isSent = $jawabans->where('status', 'final')->isNotEmpty();

        return view('page.verifikasi-menpan.show', compact('periode', 'opd', 'komponens', 'jawabanMap', 'verifikasiStats', 'progress', 'isAllAnswered', 'isSent'));
    }

    public function detail(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        $this->authorizeMenpan();

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');
        $totalJawaban = (clone $opdBase)->count();
        $totalDisetujui = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();

        if ($totalJawaban === 0 || $totalDisetujui < $totalJawaban) {
            return redirect()->route('verifikasi-menpan.index')
                ->with('error', 'OPD belum siap diverifikasi Menpan.');
        }

        $subKategori->load(['indikators.pertanyaans.subPertanyaans', 'kategori.komponen']);

        $indikators = $subKategori->indikators;
        $totalIndikator = $indikators->count();

        if ($totalIndikator == 0) {
            return redirect()->back()->with('error', 'Tidak ada indikator pada sub kategori ini.');
        }

        $currentPage = (int) max(1, min($request->get('indikator', 1), $totalIndikator));
        $currentIndikator = $indikators->get($currentPage - 1);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'terkirim')
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $nilaiIndikator = $this->hitungNilaiIndikatorMenpan($currentIndikator, $jawabanMap);

        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        return view('page.verifikasi-menpan.detail', compact('periode', 'opd', 'subKategori', 'currentIndikator', 'currentPage', 'totalIndikator', 'jawabanMap', 'nilaiIndikator', 'isCanVerify', 'startVerif', 'endVerif'));
    }

    public function store(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        $this->authorizeMenpan();

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');
        $totalJawaban = (clone $opdBase)->count();
        $totalDisetujui = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();

        if ($totalJawaban === 0 || $totalDisetujui < $totalJawaban) {
            return redirect()->route('verifikasi-menpan.index')
                ->with('error', 'OPD belum siap diverifikasi Menpan.');
        }

        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        if (!$isCanVerify) {
            return redirect()->back()->with('error', 'Verifikasi tidak dapat dilakukan karena di luar masa waktu verifikasi.');
        }

        $menpanData = $request->input('menpan');
        $currentPage = $request->input('current_page', 1);

        if ($menpanData && is_array($menpanData)) {
            foreach ($menpanData as $pertanyaanId => $data) {
                $jawabans = Jawaban::where('periode_id', $periode->id)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->where('status_verifikasi', 'terkirim')
                    ->get();

                foreach ($jawabans as $jawaban) {
                    $jawaban->status_verifikasi_menpan = $data['status_verifikasi_menpan'] ?? 'belum_diverifikasi';

                    if (isset($data['menpan_jawaban_angka']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['menpan_jawaban_angka'])) {
                        $val = $data['menpan_jawaban_angka'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->menpan_jawaban_angka = ($val !== null && $val !== '') ? $val : null;
                    }

                    if (isset($data['menpan_jawaban_text']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['menpan_jawaban_text'])) {
                        $val = $data['menpan_jawaban_text'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->menpan_jawaban_text = ($val !== null && $val !== '') ? $val : null;
                    }

                    if ($jawaban->status_verifikasi_menpan !== 'belum_diverifikasi') {
                        $jawaban->menpan_verified_by = Auth::id();
                        $jawaban->menpan_verified_at = now();
                    } else {
                        $jawaban->menpan_verified_by = null;
                        $jawaban->menpan_verified_at = null;
                    }

                    $jawaban->save();
                }
            }
        }

        return redirect()->route('verifikasi-menpan.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Data verifikasi Menpan berhasil disimpan.');
    }

    private function hitungNilaiIndikatorMenpan(Indikator $indikator, array $jawabanMap): array
    {
        $pertanyaans = $indikator->pertanyaans;
        $totalPertanyaan = $pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $pertanyaanTerverifikasi = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($pertanyaans as $pertanyaan) {
            $nilai = null;
            $terjawab = false;
            $isVerified = false;

            if ($pertanyaan->has_sub_pertanyaan) {
                $jawabanSub = [];
                foreach ($pertanyaan->subPertanyaans as $subPertanyaan) {
                    $key = $pertanyaan->id . '_' . $subPertanyaan->id;
                    $jawabanSubModel = $jawabanMap[$key] ?? null;
                    if ($jawabanSubModel) {
                        if ($jawabanSubModel->status_verifikasi_menpan === 'disetujui') {
                            $isVerified = true;
                        }
                        $value = $jawabanSubModel->menpan_jawaban_angka ?? $jawabanSubModel->verifikator_jawaban_angka ?? $jawabanSubModel->jawaban_angka;
                        if ($value !== null && $value !== '') {
                            $jawabanSub[$subPertanyaan->id] = $value;
                        }
                    }
                }

                if (count($jawabanSub) >= 2) {
                    $nilai = $this->hitungNilaiSubPertanyaan($pertanyaan, $jawabanSub);
                    $terjawab = true;
                }
            } else {
                $jawaban = $jawabanMap[$pertanyaan->id] ?? null;
                if ($jawaban) {
                    if ($jawaban->status_verifikasi_menpan === 'disetujui') {
                        $isVerified = true;
                    }
                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                        $value = $jawaban->menpan_jawaban_text ?? $jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text;
                    } else {
                        $value = $jawaban->menpan_jawaban_angka ?? $jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka;
                    }

                    if ($value !== null && $value !== '') {
                        $nilai = $this->hitungNilai($pertanyaan, $value);
                        $terjawab = true;
                    }
                }
            }

            $nilaiPerPertanyaan[$pertanyaan->id] = [
                'nilai' => $nilai,
                'terjawab' => $terjawab,
            ];

            if ($nilai !== null) {
                $totalNilai += $nilai;
                $pertanyaanTerjawab++;
            }

            if ($isVerified) {
                $pertanyaanTerverifikasi++;
            }
        }

        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;
        $persenCapaian = $indikator->bobot > 0 ? ($nilaiIndikator / $indikator->bobot) * 100 : 0;

        return [
            'total_pertanyaan' => $totalPertanyaan,
            'pertanyaan_terjawab' => $pertanyaanTerjawab,
            'pertanyaan_terverifikasi' => $pertanyaanTerverifikasi,
            'rata_rata_nilai' => round($rataRataNilai, 2),
            'bobot' => $indikator->bobot,
            'nilai_indikator' => round($nilaiIndikator, 2),
            'persen_capaian' => round($persenCapaian, 2),
            'nilai_per_pertanyaan' => $nilaiPerPertanyaan,
        ];
    }

    private function hitungNilai(Pertanyaan $pertanyaan, $jawaban): ?float
    {
        if ($jawaban === null || $jawaban === '') {
            return null;
        }

        $tipe = $pertanyaan->tipe_jawaban;

        if ($tipe === 'ya_tidak') {
            return strtolower((string) $jawaban) === 'ya' ? 1.0 : 0.0;
        }

        if ($tipe === 'pilihan_ganda') {
            $jumlahOpsi = count($pertanyaan->penjelasan_list);
            $opsi = strtoupper((string) $jawaban);
            $skorMap = $this->getSkorMap($jumlahOpsi);

            return $skorMap[$opsi] ?? null;
        }

        if ($tipe === 'angka') {
            $angka = floatval($jawaban);

            if (
                str_contains($pertanyaan->pertanyaan, 'Nilai Survey Persepsi Korupsi (Survei Eksternal)') ||
                str_contains($pertanyaan->pertanyaan, 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
            ) {
                return $angka / 4;
            }

            if ($angka > 1 && $angka <= 100) {
                return $angka / 100;
            }

            return $angka;
        }

        return null;
    }

    private function getSkorMap(int $jumlahOpsi): array
    {
        switch ($jumlahOpsi) {
            case 2:
                return [
                    'A' => 1.0,
                    'B' => 0.0,
                ];
            case 3:
                return [
                    'A' => 1.0,
                    'B' => 0.5,
                    'C' => 0.0,
                ];
            case 4:
                return [
                    'A' => 1.0,
                    'B' => 0.67,
                    'C' => 0.33,
                    'D' => 0.0,
                ];
            case 5:
                return [
                    'A' => 1.0,
                    'B' => 0.75,
                    'C' => 0.5,
                    'D' => 0.25,
                    'E' => 0.0,
                ];
            default:
                $map = [];
                $letters = range('A', chr(64 + $jumlahOpsi));
                foreach ($letters as $index => $letter) {
                    $map[$letter] = round(1 - ($index / ($jumlahOpsi - 1)), 2);
                }
                return $map;
        }
    }

    private function hitungNilaiSubPertanyaan(Pertanyaan $pertanyaan, array $jawabanSubArray): ?float
    {
        if (count($jawabanSubArray) < 2) {
            return null;
        }

        $subPertanyaans = $pertanyaan->subPertanyaans()->orderBy('urutan')->get();
        if ($subPertanyaans->count() < 2) {
            return null;
        }

        $idAcuan = $subPertanyaans->first()->id;
        $idRealisasi = $subPertanyaans->get(1)->id;

        if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
            $idRealisasi = $subPertanyaans->get(1)->id;
        } elseif (str_contains($pertanyaan->pertanyaan, 'Persentase penyampaian LHKPN')) {
            $idRealisasi = $subPertanyaans->where('urutan', 5)->first()->id ?? $subPertanyaans->last()->id;
        }

        $nilaiAcuan = floatval($jawabanSubArray[$idAcuan] ?? 0);
        $nilaiRealisasi = floatval($jawabanSubArray[$idRealisasi] ?? 0);

        if ($nilaiAcuan > 0) {
            if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
                $capaian = ($nilaiAcuan - $nilaiRealisasi) / $nilaiAcuan;
                return max(0, min($capaian, 1.0));
            }

            $capaian = $nilaiRealisasi / $nilaiAcuan;
            return min($capaian, 1.0);
        }

        return null;
    }
}
