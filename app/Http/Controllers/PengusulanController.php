<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jawaban;
use App\Models\Komponen;
use App\Models\Opd;
use App\Models\Periode;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class PengusulanController extends Controller
{
    /**
     * Get the list of work units (OPD) proposed/eligible for WBK.
     * GET /pengusulan/unit-wbk
     */
    public function getUnitWbk(Request $request)
    {
        $areaOrder = [
            'MANAJEMEN PERUBAHAN',
            'PENATAAN TATALAKSANA',
            'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
            'PENGUATAN AKUNTABILITAS',
            'PENGUATAN PENGAWASAN',
            'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
        ];

        // Find active/target period
        $tahun = $request->query('tahun');
        if ($tahun) {
            $activePeriode = Periode::where('tahun', $tahun)
                ->where('is_template', false)
                ->first();

            if (! $activePeriode) {
                return response()->json([
                    'tahun' => (int) $tahun,
                    'units' => [],
                ]);
            }
        } else {
            $activePeriode = Periode::where('status', 'aktif')
                ->where('is_template', false)
                ->orderBy('tahun', 'desc')
                ->first();

            // Fallback to the latest non-template period if none found
            if (! $activePeriode) {
                $activePeriode = Periode::where('is_template', false)
                    ->orderBy('tahun', 'desc')
                    ->first();
            }
        }

        if (! $activePeriode) {
            return response()->json([
                'tahun' => (int) ($tahun ?: date('Y')),
                'units' => [],
            ]);
        }

        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators' => fn ($q) => $q->where('status', 1),
                'kategoris.subKategoris.indikators.pertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators.pertanyaans.subPertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
            ])
            ->orderBy('urutan')
            ->get();

        $bobotMeta = $this->getRekapBobotMeta($komponens, $areaOrder);

        // Fetch all active OPDs
        $assignedOpdIds = Opd::where('status', 1)->pluck('id');

        $eligibleUnits = [];

        if ($assignedOpdIds->isNotEmpty()) {
            // Find OPDs that have final submissions
            $query = Jawaban::where('periode_id', $activePeriode->id)
                ->whereNull('sub_pertanyaan_id')
                ->whereIn('opd_id', $assignedOpdIds)
                ->where('status', 'final');

            $submittedOpdIds = $query->distinct()->pluck('opd_id');

            $opds = Opd::whereIn('id', $submittedOpdIds)
                ->orderBy('n_opd')
                ->get();

            // Default evaluate role to 'verifikator' (TPI / internal reviewer assessment)
            $role = $request->query('role', 'verifikator');
            if (! in_array($role, ['operator', 'verifikator', 'menpan'])) {
                $role = 'verifikator';
            }

            foreach ($opds as $opd) {
                // Fetch answers map
                $jawabans = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->get();

                $jawabanMap = [];
                foreach ($jawabans as $j) {
                    $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
                    $jawabanMap[$key] = $j;
                }

                $progressSet = $this->buildProgressRekapRole($role, $komponens, $jawabanMap);
                $rekap = $this->buildRekapFromProgress($progressSet, $komponens);

                $areaData = [];
                $totalPengungkitBobot = 0;
                $totalPengungkitNilai = 0;
                foreach ($areaOrder as $areaName) {
                    $area = $rekap['rekapPengungkit'][$areaName] ?? [
                        'pemenuhan_bobot' => 0,
                        'pemenuhan_nilai' => 0,
                        'reform_bobot' => 0,
                        'reform_nilai' => 0,
                    ];
                    $bobotArea = (float) $area['pemenuhan_bobot'] + (float) $area['reform_bobot'];
                    $nilaiArea = (float) $area['pemenuhan_nilai'] + (float) $area['reform_nilai'];
                    $persenArea = $bobotArea > 0 ? ($nilaiArea / $bobotArea) * 100 : 0;

                    $areaData[] = [
                        'nama' => $areaName,
                        'bobot' => $bobotArea,
                        'nilai' => $nilaiArea,
                        'persen' => $persenArea,
                    ];

                    $totalPengungkitBobot += $bobotArea;
                    $totalPengungkitNilai += $nilaiArea;
                }

                $hasilMap = collect($rekap['rekapHasil'] ?? [])->keyBy('nama');
                $birokrasi = $hasilMap->get('BIROKRASI YANG BERSIH DAN AKUNTABEL', [
                    'nilai' => 0,
                    'bobot' => $bobotMeta['hasil']['birokrasi'],
                    'subs' => [],
                ]);
                $pelayanan = $hasilMap->get('PELAYANAN PUBLIK YANG PRIMA', [
                    'nilai' => 0,
                    'bobot' => $bobotMeta['hasil']['pelayanan'],
                    'subs' => [],
                ]);

                $birokrasiSubs = collect($birokrasi['subs'] ?? []);
                $spak = $birokrasiSubs->firstWhere('nama', 'Nilai Survey Persepsi Korupsi (Survei Eksternal)')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['spak']];
                $capaian = $birokrasiSubs->firstWhere('nama', 'Capaian Kinerja Lebih Baik dari pada Capaian Kinerja Sebelumnya')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['capaian']];

                $pelayananSubs = collect($pelayanan['subs'] ?? []);
                $spp = $pelayananSubs->firstWhere('nama', 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['pelayanan']];

                $birokrasiNilai = (float) ($birokrasi['nilai'] ?? 0);
                $birokrasiBobot = (float) ($birokrasi['bobot'] ?? $bobotMeta['hasil']['birokrasi']);

                $spakNilai = (float) ($spak['nilai'] ?? 0);
                $spakBobot = (float) ($spak['bobot'] ?? $bobotMeta['hasil']['spak']);

                $capaianNilai = (float) ($capaian['nilai'] ?? 0);
                $capaianBobot = (float) ($capaian['bobot'] ?? $bobotMeta['hasil']['capaian']);

                $pelayananNilai = (float) ($spp['nilai'] ?? $pelayanan['nilai'] ?? 0);
                $pelayananBobot = (float) ($pelayanan['bobot'] ?? $bobotMeta['hasil']['pelayanan']);

                $totalHasilNilai = (float) ($birokrasiNilai + (float) ($pelayanan['nilai'] ?? $pelayananNilai));

                $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;

                $areaCompliance = [];
                foreach ($areaData as $area) {
                    $areaCompliance[$area['nama']] = [
                        'nilai' => (float) $area['nilai'],
                        'bobot' => (float) $area['bobot'],
                        'persen' => (float) $area['persen'],
                        'threshold' => (float) ($area['bobot'] * 0.60),
                        'is_passed' => $area['bobot'] == 0 || $area['persen'] >= 60,
                    ];
                }

                $compliance = [
                    'total_zi' => [
                        'nilai' => (float) $grandTotalNilai,
                        'threshold' => 75.00,
                        'is_passed' => $grandTotalNilai >= 75.00,
                    ],
                    'total_pengungkit' => [
                        'nilai' => (float) $totalPengungkitNilai,
                        'threshold' => 40.00,
                        'is_passed' => $totalPengungkitNilai >= 40.00,
                    ],
                    'areas' => $areaCompliance,
                    'birokrasi_total' => [
                        'nilai' => (float) $birokrasiNilai,
                        'threshold' => 18.25,
                        'is_passed' => $birokrasiNilai >= 18.25,
                    ],
                    'spak' => [
                        'nilai' => (float) $spakNilai,
                        'threshold' => 15.75,
                        'is_passed' => $spakNilai >= 15.75,
                    ],
                    'capaian' => [
                        'nilai' => (float) $capaianNilai,
                        'threshold' => 2.50,
                        'is_passed' => $capaianNilai >= 2.50,
                    ],
                    'pelayanan' => [
                        'nilai' => (float) $pelayananNilai,
                        'threshold' => 14.00,
                        'is_passed' => $pelayananNilai >= 14.00,
                    ],
                ];

                $meetsArea = collect($areaCompliance)->every(function ($area) {
                    return $area['is_passed'];
                });

                $meetsWbk = $compliance['total_zi']['is_passed']
                    && $compliance['total_pengungkit']['is_passed']
                    && $meetsArea
                    && $compliance['birokrasi_total']['is_passed']
                    && $compliance['spak']['is_passed']
                    && $compliance['capaian']['is_passed']
                    && $compliance['pelayanan']['is_passed'];

                if ($meetsWbk) {
                    // Check if OPD is affirmation based on name keywords
                    $isAfirmasi = false;
                    $nameLower = strtolower($opd->n_opd);
                    if (
                        str_contains($nameLower, 'rsu') ||
                        str_contains($nameLower, 'rsud') ||
                        str_contains($nameLower, 'rsup') ||
                        str_contains($nameLower, 'sd') ||
                        str_contains($nameLower, 'smp') ||
                        str_contains($nameLower, 'labkesda')
                    ) {
                        $isAfirmasi = false;
                    }

                    $kategori = $isAfirmasi ? 'WBK' : 'WBK';

                    $eligibleUnits[] = [
                        'id' => sprintf('UNIT-%03d', $opd->id),
                        'nama' => $opd->n_opd,
                        'kategori' => $kategori,
                        'afirmasi' => $isAfirmasi,
                    ];
                }
            }
        }

        return response()->json([
            'tahun' => (int) $activePeriode->tahun,
            'units' => $eligibleUnits,
        ]);
    }

    /**
     * Get the list of work units (OPD) proposed/eligible for WBBM.
     * GET /pengusulan/unit-wbbm
     */
    public function getUnitWbbm(Request $request)
    {
        $areaOrder = [
            'MANAJEMEN PERUBAHAN',
            'PENATAAN TATALAKSANA',
            'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
            'PENGUATAN AKUNTABILITAS',
            'PENGUATAN PENGAWASAN',
            'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
        ];

        // Find active/target period
        $tahun = $request->query('tahun');
        if ($tahun) {
            $activePeriode = Periode::where('tahun', $tahun)
                ->where('is_template', false)
                ->first();

            if (! $activePeriode) {
                return response()->json([
                    'tahun' => (int) $tahun,
                    'units' => [],
                ]);
            }
        } else {
            $activePeriode = Periode::where('status', 'aktif')
                ->where('is_template', false)
                ->orderBy('tahun', 'desc')
                ->first();

            // Fallback to the latest non-template period if none found
            if (! $activePeriode) {
                $activePeriode = Periode::where('is_template', false)
                    ->orderBy('tahun', 'desc')
                    ->first();
            }
        }

        if (! $activePeriode) {
            return response()->json([
                'tahun' => (int) ($tahun ?: date('Y')),
                'units' => [],
            ]);
        }

        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators' => fn ($q) => $q->where('status', 1),
                'kategoris.subKategoris.indikators.pertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators.pertanyaans.subPertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
            ])
            ->orderBy('urutan')
            ->get();

        $bobotMeta = $this->getRekapBobotMeta($komponens, $areaOrder);

        // Fetch all active OPDs
        $assignedOpdIds = Opd::where('status', 1)->pluck('id');

        $eligibleUnits = [];

        if ($assignedOpdIds->isNotEmpty()) {
            // Find OPDs that have final submissions
            $query = Jawaban::where('periode_id', $activePeriode->id)
                ->whereNull('sub_pertanyaan_id')
                ->whereIn('opd_id', $assignedOpdIds)
                ->where('status', 'final');

            $submittedOpdIds = $query->distinct()->pluck('opd_id');

            $opds = Opd::whereIn('id', $submittedOpdIds)
                ->orderBy('n_opd')
                ->get();

            // Default evaluate role to 'verifikator' (TPI / internal reviewer assessment)
            $role = $request->query('role', 'verifikator');
            if (! in_array($role, ['operator', 'verifikator', 'menpan'])) {
                $role = 'verifikator';
            }

            foreach ($opds as $opd) {
                // Fetch answers map
                $jawabans = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->get();

                $jawabanMap = [];
                foreach ($jawabans as $j) {
                    $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
                    $jawabanMap[$key] = $j;
                }

                $progressSet = $this->buildProgressRekapRole($role, $komponens, $jawabanMap);
                $rekap = $this->buildRekapFromProgress($progressSet, $komponens);

                $areaData = [];
                $totalPengungkitBobot = 0;
                $totalPengungkitNilai = 0;
                foreach ($areaOrder as $areaName) {
                    $area = $rekap['rekapPengungkit'][$areaName] ?? [
                        'pemenuhan_bobot' => 0,
                        'pemenuhan_nilai' => 0,
                        'reform_bobot' => 0,
                        'reform_nilai' => 0,
                    ];
                    $bobotArea = (float) $area['pemenuhan_bobot'] + (float) $area['reform_bobot'];
                    $nilaiArea = (float) $area['pemenuhan_nilai'] + (float) $area['reform_nilai'];
                    $persenArea = $bobotArea > 0 ? ($nilaiArea / $bobotArea) * 100 : 0;

                    $areaData[] = [
                        'nama' => $areaName,
                        'bobot' => $bobotArea,
                        'nilai' => $nilaiArea,
                        'persen' => $persenArea,
                    ];

                    $totalPengungkitBobot += $bobotArea;
                    $totalPengungkitNilai += $nilaiArea;
                }

                $hasilMap = collect($rekap['rekapHasil'] ?? [])->keyBy('nama');
                $birokrasi = $hasilMap->get('BIROKRASI YANG BERSIH DAN AKUNTABEL', [
                    'nilai' => 0,
                    'bobot' => $bobotMeta['hasil']['birokrasi'],
                    'subs' => [],
                ]);
                $pelayanan = $hasilMap->get('PELAYANAN PUBLIK YANG PRIMA', [
                    'nilai' => 0,
                    'bobot' => $bobotMeta['hasil']['pelayanan'],
                    'subs' => [],
                ]);

                $birokrasiSubs = collect($birokrasi['subs'] ?? []);
                $spak = $birokrasiSubs->firstWhere('nama', 'Nilai Survey Persepsi Korupsi (Survei Eksternal)')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['spak']];
                $capaian = $birokrasiSubs->firstWhere('nama', 'Capaian Kinerja Lebih Baik dari pada Capaian Kinerja Sebelumnya')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['capaian']];

                $pelayananSubs = collect($pelayanan['subs'] ?? []);
                $spp = $pelayananSubs->firstWhere('nama', 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
                    ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['pelayanan']];

                $birokrasiNilai = (float) ($birokrasi['nilai'] ?? 0);
                $birokrasiBobot = (float) ($birokrasi['bobot'] ?? $bobotMeta['hasil']['birokrasi']);

                $spakNilai = (float) ($spak['nilai'] ?? 0);
                $spakBobot = (float) ($spak['bobot'] ?? $bobotMeta['hasil']['spak']);

                $capaianNilai = (float) ($capaian['nilai'] ?? 0);
                $capaianBobot = (float) ($capaian['bobot'] ?? $bobotMeta['hasil']['capaian']);

                $pelayananNilai = (float) ($spp['nilai'] ?? $pelayanan['nilai'] ?? 0);
                $pelayananBobot = (float) ($pelayanan['bobot'] ?? $bobotMeta['hasil']['pelayanan']);

                $totalHasilNilai = (float) ($birokrasiNilai + (float) ($pelayanan['nilai'] ?? $pelayananNilai));

                $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;

                $areaCompliance = [];
                foreach ($areaData as $area) {
                    $areaCompliance[$area['nama']] = [
                        'nilai' => (float) $area['nilai'],
                        'bobot' => (float) $area['bobot'],
                        'persen' => (float) $area['persen'],
                        'threshold' => (float) ($area['bobot'] * 0.75),
                        'is_passed' => $area['bobot'] == 0 || $area['persen'] >= 75,
                    ];
                }

                $compliance = [
                    'total_zi' => [
                        'nilai' => (float) $grandTotalNilai,
                        'threshold' => 85.00,
                        'is_passed' => $grandTotalNilai >= 85.00,
                    ],
                    'total_pengungkit' => [
                        'nilai' => (float) $totalPengungkitNilai,
                        'threshold' => 48.00,
                        'is_passed' => $totalPengungkitNilai >= 48.00,
                    ],
                    'areas' => $areaCompliance,
                    'birokrasi_total' => [
                        'nilai' => (float) $birokrasiNilai,
                        'threshold' => 18.25,
                        'is_passed' => $birokrasiNilai >= 18.25,
                    ],
                    'spak' => [
                        'nilai' => (float) $spakNilai,
                        'threshold' => 15.75,
                        'is_passed' => $spakNilai >= 15.75,
                    ],
                    'capaian' => [
                        'nilai' => (float) $capaianNilai,
                        'threshold' => 2.50,
                        'is_passed' => $capaianNilai >= 2.50,
                    ],
                    'pelayanan' => [
                        'nilai' => (float) $pelayananNilai,
                        'threshold' => 15.75,
                        'is_passed' => $pelayananNilai >= 15.75,
                    ],
                ];

                $meetsArea = collect($areaCompliance)->every(function ($area) {
                    return $area['is_passed'];
                });

                $meetsWbbm = $compliance['total_zi']['is_passed']
                    && $compliance['total_pengungkit']['is_passed']
                    && $meetsArea
                    && $compliance['birokrasi_total']['is_passed']
                    && $compliance['spak']['is_passed']
                    && $compliance['capaian']['is_passed']
                    && $compliance['pelayanan']['is_passed'];

                if ($meetsWbbm) {
                    // Check if OPD is affirmation based on name keywords
                    $isAfirmasi = false;
                    $nameLower = strtolower($opd->n_opd);
                    if (
                        str_contains($nameLower, 'rsu') ||
                        str_contains($nameLower, 'rsud') ||
                        str_contains($nameLower, 'rsup') ||
                        str_contains($nameLower, 'sd') ||
                        str_contains($nameLower, 'smp') ||
                        str_contains($nameLower, 'labkesda')
                    ) {
                        $isAfirmasi = false;
                    }

                    $kategori = $isAfirmasi ? 'WBBM' : 'WBBM';

                    $eligibleUnits[] = [
                        'id' => sprintf('UNIT-%03d', $opd->id),
                        'nama' => $opd->n_opd,
                        'kategori' => $kategori,
                        'afirmasi' => $isAfirmasi,
                    ];
                }
            }
        }

        return response()->json([
            'tahun' => (int) $activePeriode->tahun,
            'units' => $eligibleUnits,
        ]);
    }

    private function buildProgressRekapRole(string $role, $komponens, array $jawabanMap): array
    {
        $progress = [];

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalNilaiSubKategori = 0;
                    foreach ($subKategori->indikators as $indikator) {
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorRole($role, $indikator, $jawabanMap);
                        $totalNilaiSubKategori += $nilaiIndikatorData['nilai_indikator'];
                    }
                    $progress[$subKategori->id] = $totalNilaiSubKategori;
                }
            }
        }

        return $progress;
    }

    private function hitungNilaiIndikatorRole(string $role, Indikator $indikator, array $jawabanMap): array
    {
        $pertanyaans = $indikator->pertanyaans;
        $totalPertanyaan = $pertanyaans->count();
        $pertanyaanTerjawab = 0;
        $totalNilai = 0;
        $nilaiPerPertanyaan = [];

        foreach ($pertanyaans as $pertanyaan) {
            $nilai = null;
            $terjawab = false;

            if ($pertanyaan->has_sub_pertanyaan) {
                $jawabanSub = [];
                foreach ($pertanyaan->subPertanyaans as $subPertanyaan) {
                    $key = $pertanyaan->id.'_'.$subPertanyaan->id;
                    $jawabanSubModel = $jawabanMap[$key] ?? null;
                    if ($jawabanSubModel) {
                        $value = null;
                        if ($role === 'operator') {
                            $value = $jawabanSubModel->jawaban_angka;
                        } elseif ($role === 'verifikator') {
                            $value = $jawabanSubModel->verifikator_jawaban_angka;
                        } elseif ($role === 'menpan') {
                            $value = $jawabanSubModel->menpan_jawaban_angka;
                        }

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
                    $value = null;
                    if ($role === 'operator') {
                        $value = in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda']) ? $jawaban->jawaban_text : $jawaban->jawaban_angka;
                    } elseif ($role === 'verifikator') {
                        if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                            $value = $jawaban->verifikator_jawaban_text;
                        } else {
                            $value = $jawaban->verifikator_jawaban_angka;
                        }
                    } elseif ($role === 'menpan') {
                        if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                            $value = $jawaban->menpan_jawaban_text;
                        } else {
                            $value = $jawaban->menpan_jawaban_angka;
                        }
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
        }

        $rataRataNilai = $pertanyaanTerjawab > 0 ? $totalNilai / $pertanyaanTerjawab : 0;
        $nilaiIndikator = $rataRataNilai * $indikator->bobot;
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

    private function buildRekapFromProgress(array $progressSet, $komponens): array
    {
        $pengungkit = [];
        $hasil = [];

        foreach ($komponens as $komponen) {
            if ($komponen->nama === 'PENGUNGKIT') {
                foreach ($komponen->kategoris as $kategori) {
                    foreach ($kategori->subKategoris as $subKategori) {
                        $namaArea = trim($subKategori->nama);
                        if (! isset($pengungkit[$namaArea])) {
                            $pengungkit[$namaArea] = [
                                'nama' => $namaArea,
                                'pemenuhan_bobot' => 0,
                                'pemenuhan_nilai' => 0,
                                'reform_bobot' => 0,
                                'reform_nilai' => 0,
                            ];
                        }

                        if (stripos($kategori->nama, 'PEMENUHAN') !== false) {
                            $pengungkit[$namaArea]['pemenuhan_bobot'] = $subKategori->bobot;
                            $pengungkit[$namaArea]['pemenuhan_nilai'] = $progressSet[$subKategori->id] ?? 0;
                        } elseif (stripos($kategori->nama, 'REFORM') !== false) {
                            $pengungkit[$namaArea]['reform_bobot'] = $subKategori->bobot;
                            $pengungkit[$namaArea]['reform_nilai'] = $progressSet[$subKategori->id] ?? 0;
                        }
                    }
                }
            } elseif ($komponen->nama === 'HASIL') {
                foreach ($komponen->kategoris as $kategori) {
                    $subs = [];
                    $nilaiKategori = 0;
                    foreach ($kategori->subKategoris as $subKategori) {
                        $subNilai = $progressSet[$subKategori->id] ?? 0;
                        $nilaiKategori += $subNilai;
                        $subs[] = [
                            'kode' => $subKategori->kode,
                            'nama' => $subKategori->nama,
                            'bobot' => $subKategori->bobot,
                            'nilai' => $subNilai,
                        ];
                    }
                    $hasil[] = [
                        'kode' => $kategori->kode,
                        'nama' => $kategori->nama,
                        'bobot' => $kategori->bobot,
                        'nilai' => $nilaiKategori,
                        'subs' => $subs,
                    ];
                }
            }
        }

        return ['rekapPengungkit' => $pengungkit, 'rekapHasil' => $hasil];
    }

    private function getRekapBobotMeta($komponens, array $areaOrder): array
    {
        $areaBobot = array_fill_keys($areaOrder, 0);
        $hasil = [
            'birokrasi' => 0,
            'spak' => 0,
            'capaian' => 0,
            'pelayanan' => 0,
        ];

        foreach ($komponens as $komponen) {
            if ($komponen->nama === 'PENGUNGKIT') {
                foreach ($komponen->kategoris as $kategori) {
                    foreach ($kategori->subKategoris as $subKategori) {
                        $name = trim($subKategori->nama);
                        if (array_key_exists($name, $areaBobot)) {
                            $areaBobot[$name] += $subKategori->bobot;
                        }
                    }
                }
            }

            if ($komponen->nama === 'HASIL') {
                foreach ($komponen->kategoris as $kategori) {
                    $kategoriName = trim($kategori->nama);
                    if ($kategoriName === 'BIROKRASI YANG BERSIH DAN AKUNTABEL') {
                        $hasil['birokrasi'] = $kategori->bobot;
                        foreach ($kategori->subKategoris as $subKategori) {
                            if (str_contains($subKategori->nama, 'Survey Persepsi Korupsi')) {
                                $hasil['spak'] = $subKategori->bobot;
                            }
                            if (str_contains($subKategori->nama, 'Capaian Kinerja')) {
                                $hasil['capaian'] = $subKategori->bobot;
                            }
                        }
                    }

                    if ($kategoriName === 'PELAYANAN PUBLIK YANG PRIMA') {
                        $hasil['pelayanan'] = $kategori->bobot;
                    }
                }
            }
        }

        $pengungkitTotal = array_sum($areaBobot);
        $hasilTotal = $hasil['birokrasi'] + $hasil['pelayanan'];

        return [
            'area' => $areaBobot,
            'pengungkit_total' => $pengungkitTotal,
            'hasil' => $hasil,
            'hasil_total' => $hasilTotal,
            'total' => 100.0,
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
        $idRealisasi = $subPertanyaans->last()->id;

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

    /**
     * Get the LKE answers and verifications for a specific work unit.
     * GET /lke/jawaban-unit/{unit_id}
     */
    public function getJawabanUnit($unit_id, Request $request)
    {
        $cleanId = $unit_id;
        if (str_starts_with(strtoupper($unit_id), 'UNIT-')) {
            $cleanId = (int) substr($unit_id, 5);
        }

        $opd = Opd::find($cleanId);
        if (! $opd) {
            return response()->json([
                'error' => true,
                'code' => 'DATA_NOT_FOUND',
                'message' => 'Data yang diminta tidak ditemukan',
            ], 404);
        }

        $tahun = date('Y');
        $periode = Periode::where('tahun', $tahun)->first();
        if (! $periode) {
            return response()->json([
                'error' => true,
                'code' => 'DATA_NOT_FOUND',
                'message' => 'Data yang diminta tidak ditemukan',
            ], 404);
        }

        $listSoalPath = base_path('list_soal.json');
        if (! file_exists($listSoalPath)) {
            return response()->json([
                'error' => true,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error di sisi instansi',
            ], 500);
        }

        $listSoal = json_decode(file_get_contents($listSoalPath), true);

        // Fetch all answers for this OPD and period
        $jawabans = Jawaban::where('opd_id', $opd->id)
            ->where('periode_id', $periode->id)
            ->with('files')
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id !== null
                ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}"
                : "{$j->pertanyaan_id}_null";
            $jawabanMap[$key] = $j;
        }

        $answers = [];
        foreach ($listSoal as $item) {
            $key = $item['sub_pertanyaan_id'] !== null
                ? "{$item['pertanyaan_id']}_{$item['sub_pertanyaan_id']}"
                : "{$item['pertanyaan_id']}_null";

            $j = $jawabanMap[$key] ?? null;

            $jawabanUnit = null;
            $jawabanTpi = null;
            $catatanUnit = null;
            $catatanTpi = null;
            $buktiDukungUnit = null;

            if ($j) {
                // Rule 2: jawaban_unit
                $jawabanUnit = $j->jawaban_text !== null && $j->jawaban_text !== '' ? $j->jawaban_text : $j->jawaban_angka;
                if ($jawabanUnit === '') {
                    $jawabanUnit = null;
                }

                // Rule 3: jawaban_tpi
                $jawabanTpi = $j->verifikator_jawaban_text !== null && $j->verifikator_jawaban_text !== '' ? $j->verifikator_jawaban_text : $j->verifikator_jawaban_angka;
                if ($jawabanTpi === '') {
                    $jawabanTpi = null;
                }

                // Rule 4: catatan_unit
                $catatanUnit = $j->keterangan !== '' ? $j->keterangan : null;

                // Rule 5: catatan_tpi
                $catatanTpi = $j->catatan_verifikator !== '' ? $j->catatan_verifikator : null;

                // Rule 6: bukti_dukung_unit
                if ($j->files->isNotEmpty()) {
                    $buktiDukungUnit = $j->files->map(function ($file) {
                        return route('kuesioner.file.item.view', $file->id);
                    })->filter()->implode(';');
                }
                if ($buktiDukungUnit === '') {
                    $buktiDukungUnit = null;
                }
            }

            $answers[] = [
                'soal_id' => (int) $item['soal_id'],
                'jawaban_unit' => $jawabanUnit,
                'jawaban_tpi' => $jawabanTpi,
                'catatan_unit' => $catatanUnit,
                'bukti_dukung_unit' => $buktiDukungUnit,
                'catatan_tpi' => $catatanTpi,
            ];
        }

        return response()->json([
            'unit_id' => sprintf('UNIT-%03d', $opd->id),
            'nama_unit' => $opd->n_opd,
            'tahun' => (int) $periode->tahun,
            'answers' => $answers,
        ]);
    }
}
