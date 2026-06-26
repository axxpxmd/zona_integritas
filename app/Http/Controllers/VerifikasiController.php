<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jawaban;
use App\Models\Komponen;
use App\Models\Opd;
use App\Models\Periode;
use App\Models\Pertanyaan;
use App\Models\SubKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Get available periods
        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $periodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        $activePeriode = $periodeId ? Periode::find($periodeId) : null;

        $submittedOpds = collect();

        if ($activePeriode) {
            // Get distinct OPDs that have submitted their answers (status = 'final') for the selected period
            $query = Jawaban::where('periode_id', $activePeriode->id)
                ->where('status', 'final')
                ->distinct();

            if (Auth::user()->role === 'verifikator') {
                $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                    ->where('user_id', Auth::id())
                    ->pluck('opd_id');
                $query->whereIn('opd_id', $assignedOpdIds);
            }

            $opdIds = $query->pluck('opd_id');

            $submittedOpds = Opd::whereIn('id', $opdIds)->get();

            $totalRequired = \App\Models\Pertanyaan::where('status', 1)->count();

            // For each OPD, we can also get the date they submitted (max updated_at where status=final)
            foreach ($submittedOpds as $opd) {
                $lastSubmit = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->where('status', 'final')
                    ->max('updated_at');

                $opd->submitted_at = $lastSubmit;

                // Verifikasi stats per OPD
                $opd_base = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $opd->total_jawaban = $opd_base->count();
                $opd->terverifikasi = (clone $opd_base)->whereIn('status_verifikasi', ['disetujui', 'terkirim'])->count();
                $opd->terkirim = (clone $opd_base)->where('status_verifikasi', 'terkirim')->count();
                $opd->direvisi = (clone $opd_base)->where('status_verifikasi', 'direvisi')->count();
                $opd->belum_terverifikasi = (clone $opd_base)->where('status_verifikasi', 'belum_diverifikasi')->count();
                $opd->menunggu_dicek_ulang = (clone $opd_base)->where('menunggu_dicek_ulang', true)->count();
                $opd->total_pertanyaan = $totalRequired;
                $opd->persen = $opd->total_jawaban > 0
                    ? min(100, round((($opd->terverifikasi + $opd->direvisi) / $opd->total_jawaban) * 100))
                    : 0;
            }
        }

        return view('page.verifikasi.index', compact('periodes', 'activePeriode', 'submittedOpds'));
    }

    public function rekapDashboard(Request $request)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }

        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $periodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        $activePeriode = $periodeId ? Periode::find($periodeId) : null;

        $areaOrder = [
            'MANAJEMEN PERUBAHAN',
            'PENATAAN TATALAKSANA',
            'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
            'PENGUATAN AKUNTABILITAS',
            'PENGUATAN PENGAWASAN',
            'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
        ];

        $rekapData = [
            'operator' => collect(),
            'verifikator' => collect(),
            'menpan' => collect(),
        ];
        $bobotMeta = [
            'area' => array_fill_keys($areaOrder, 0),
            'pengungkit_total' => 0,
            'hasil' => [
                'birokrasi' => 0,
                'spak' => 0,
                'capaian' => 0,
                'pelayanan' => 0,
            ],
            'hasil_total' => 0,
            'total' => 100.0,
        ];
        $thresholds = [
            'area' => array_fill_keys($areaOrder, 0),
            'pengungkit_total' => 40.00,
            'spak' => 15.75,
            'capaian' => 2.50,
            'pelayanan' => 14.00,
            'hasil_total' => 32.25,
            'total' => 75.00,
        ];

        if ($activePeriode) {
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
            foreach ($bobotMeta['area'] as $areaName => $bobot) {
                $thresholds['area'][$areaName] = $bobot * 0.60;
            }

            $verifiedStatuses = ['disetujui', 'terkirim'];

            if (Auth::user()->role === 'admin') {
                $assignedOpdIds = Opd::where('status', 1)->pluck('id');
            } elseif (Auth::user()->role === 'operator') {
                $assignedOpdIds = collect([Auth::user()->opd_id]);
            } else {
                $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                    ->where('user_id', Auth::id())
                    ->pluck('opd_id');
            }

            if ($assignedOpdIds->isNotEmpty()) {
                $query = Jawaban::where('periode_id', $activePeriode->id)
                    ->whereNull('sub_pertanyaan_id')
                    ->whereIn('opd_id', $assignedOpdIds);

                if (! in_array(Auth::user()->role, ['operator', 'verifikator', 'admin'])) {
                    $query->where('status', 'final');
                }

                $submittedOpdIds = $query->distinct()->pluck('opd_id');

                $opds = Opd::whereIn('id', $submittedOpdIds)
                    ->orderBy('n_opd')
                    ->get();

                foreach ($opds as $opd) {
                    $opdBase = Jawaban::where('periode_id', $activePeriode->id)
                        ->where('opd_id', $opd->id)
                        ->whereNull('sub_pertanyaan_id');

                    $totalJawaban = (clone $opdBase)->count();
                    $totalVerified = (clone $opdBase)->whereIn('status_verifikasi', $verifiedStatuses)->count();

                    if (! in_array(Auth::user()->role, ['operator', 'verifikator', 'admin'])) {
                        if ($totalJawaban === 0 || $totalVerified < $totalJawaban) {
                            continue;
                        }
                    } else {
                        if ($totalJawaban === 0) {
                            continue;
                        }
                    }

                    $jawabans = Jawaban::where('periode_id', $activePeriode->id)
                        ->where('opd_id', $opd->id)
                        ->get();

                    $jawabanMap = [];
                    foreach ($jawabans as $j) {
                        $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
                        $jawabanMap[$key] = $j;
                    }

                    foreach (['operator', 'verifikator', 'menpan'] as $roleKey) {
                        $progressSet = $this->buildProgressRekapRole($roleKey, $komponens, $jawabanMap);
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

                        $pengungkitPersen = $totalPengungkitBobot > 0
                            ? ($totalPengungkitNilai / $totalPengungkitBobot) * 100
                            : 0;

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
                        $birokrasiPersen = $birokrasiBobot > 0 ? ($birokrasiNilai / $birokrasiBobot) * 100 : 0;

                        $spakNilai = (float) ($spak['nilai'] ?? 0);
                        $spakBobot = (float) ($spak['bobot'] ?? $bobotMeta['hasil']['spak']);
                        $spakPersen = $spakBobot > 0 ? ($spakNilai / $spakBobot) * 100 : 0;

                        $capaianNilai = (float) ($capaian['nilai'] ?? 0);
                        $capaianBobot = (float) ($capaian['bobot'] ?? $bobotMeta['hasil']['capaian']);
                        $capaianPersen = $capaianBobot > 0 ? ($capaianNilai / $capaianBobot) * 100 : 0;

                        $pelayananNilai = (float) ($spp['nilai'] ?? $pelayanan['nilai'] ?? 0);
                        $pelayananBobot = (float) ($pelayanan['bobot'] ?? $bobotMeta['hasil']['pelayanan']);
                        $pelayananPersen = $pelayananBobot > 0 ? ($pelayananNilai / $pelayananBobot) * 100 : 0;

                        $totalHasilNilai = (float) ($birokrasiNilai + (float) ($pelayanan['nilai'] ?? $pelayananNilai));
                        $totalHasilBobot = (float) ($birokrasiBobot + $pelayananBobot);
                        $hasilPersen = $totalHasilBobot > 0 ? ($totalHasilNilai / $totalHasilBobot) * 100 : 0;

                        $grandTotalBobot = $totalPengungkitBobot + $totalHasilBobot;
                        $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;
                        $grandTotalPersen = $grandTotalBobot > 0 ? ($grandTotalNilai / $grandTotalBobot) * 100 : 0;

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

                        $rekapData[$roleKey]->push([
                            'opd_id' => $opd->id,
                            'opd' => $opd->n_opd,
                            'areas' => $areaData,
                            'pengungkit' => [
                                'bobot' => $totalPengungkitBobot,
                                'nilai' => $totalPengungkitNilai,
                                'persen' => $pengungkitPersen,
                            ],
                            'birokrasi' => [
                                'bobot' => $birokrasiBobot,
                                'nilai' => $birokrasiNilai,
                                'persen' => $birokrasiPersen,
                            ],
                            'spak' => [
                                'bobot' => $spakBobot,
                                'nilai' => $spakNilai,
                                'persen' => $spakPersen,
                            ],
                            'capaian' => [
                                'bobot' => $capaianBobot,
                                'nilai' => $capaianNilai,
                                'persen' => $capaianPersen,
                            ],
                            'pelayanan' => [
                                'bobot' => $pelayananBobot,
                                'nilai' => $pelayananNilai,
                                'persen' => $pelayananPersen,
                            ],
                            'hasil' => [
                                'bobot' => $totalHasilBobot,
                                'nilai' => $totalHasilNilai,
                                'persen' => $hasilPersen,
                            ],
                            'total' => [
                                'bobot' => $grandTotalBobot,
                                'nilai' => $grandTotalNilai,
                                'persen' => $grandTotalPersen,
                            ],
                            'meets_wbk' => $meetsWbk,
                            'compliance' => $compliance,
                        ]);
                    }
                }
            }
        }

        return view('page.verifikasi.rekap-dashboard', compact(
            'periodes',
            'activePeriode',
            'rekapData',
            'areaOrder',
            'bobotMeta',
            'thresholds'
        ));
    }

    public function exportPdf(Request $request)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator', 'operator'])) {
            abort(403, 'Akses ditolak.');
        }

        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        $periodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        $activePeriode = $periodeId ? Periode::find($periodeId) : null;

        if (! $activePeriode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan.');
        }

        $role = $request->role ?? 'operator';
        if (! in_array($role, ['operator', 'verifikator', 'menpan'])) {
            $role = 'operator';
        }

        $areaOrder = [
            'MANAJEMEN PERUBAHAN',
            'PENATAAN TATALAKSANA',
            'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
            'PENGUATAN AKUNTABILITAS',
            'PENGUATAN PENGAWASAN',
            'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
        ];

        $bobotMeta = [
            'area' => array_fill_keys($areaOrder, 0),
            'pengungkit_total' => 0,
            'hasil' => [
                'birokrasi' => 0,
                'spak' => 0,
                'capaian' => 0,
                'pelayanan' => 0,
            ],
            'hasil_total' => 0,
            'total' => 100.0,
        ];
        $thresholds = [
            'area' => array_fill_keys($areaOrder, 0),
            'pengungkit_total' => 40.00,
            'spak' => 15.75,
            'capaian' => 2.50,
            'pelayanan' => 14.00,
            'hasil_total' => 32.25,
            'total' => 75.00,
        ];

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
        foreach ($bobotMeta['area'] as $areaName => $bobot) {
            $thresholds['area'][$areaName] = $bobot * 0.60;
        }

        $verifiedStatuses = ['disetujui', 'terkirim'];

        if (Auth::user()->role === 'admin') {
            $assignedOpdIds = Opd::where('status', 1)->pluck('id');
        } elseif (Auth::user()->role === 'operator') {
            $assignedOpdIds = collect([Auth::user()->opd_id]);
        } else {
            $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                ->where('user_id', Auth::id())
                ->pluck('opd_id');
        }

        $rekapRows = collect();

        if ($assignedOpdIds->isNotEmpty()) {
            $query = Jawaban::where('periode_id', $activePeriode->id)
                ->whereNull('sub_pertanyaan_id')
                ->whereIn('opd_id', $assignedOpdIds);

            if (! in_array(Auth::user()->role, ['operator', 'verifikator', 'admin'])) {
                $query->where('status', 'final');
            }

            $submittedOpdIds = $query->distinct()->pluck('opd_id');

            $opds = Opd::whereIn('id', $submittedOpdIds)
                ->orderBy('n_opd')
                ->get();

            foreach ($opds as $opd) {
                $opdBase = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $totalJawaban = (clone $opdBase)->count();
                $totalVerified = (clone $opdBase)->whereIn('status_verifikasi', $verifiedStatuses)->count();

                if (! in_array(Auth::user()->role, ['operator', 'verifikator', 'admin'])) {
                    if ($totalJawaban === 0 || $totalVerified < $totalJawaban) {
                        continue;
                    }
                } else {
                    if ($totalJawaban === 0) {
                        continue;
                    }
                }

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

                $pengungkitPersen = $totalPengungkitBobot > 0
                    ? ($totalPengungkitNilai / $totalPengungkitBobot) * 100
                    : 0;

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
                $birokrasiPersen = $birokrasiBobot > 0 ? ($birokrasiNilai / $birokrasiBobot) * 100 : 0;

                $spakNilai = (float) ($spak['nilai'] ?? 0);
                $spakBobot = (float) ($spak['bobot'] ?? $bobotMeta['hasil']['spak']);
                $spakPersen = $spakBobot > 0 ? ($spakNilai / $spakBobot) * 100 : 0;

                $capaianNilai = (float) ($capaian['nilai'] ?? 0);
                $capaianBobot = (float) ($capaian['bobot'] ?? $bobotMeta['hasil']['capaian']);
                $capaianPersen = $capaianBobot > 0 ? ($capaianNilai / $capaianBobot) * 100 : 0;

                $pelayananNilai = (float) ($spp['nilai'] ?? $pelayanan['nilai'] ?? 0);
                $pelayananBobot = (float) ($pelayanan['bobot'] ?? $bobotMeta['hasil']['pelayanan']);
                $pelayananPersen = $pelayananBobot > 0 ? ($pelayananNilai / $pelayananBobot) * 100 : 0;

                $totalHasilNilai = (float) ($birokrasiNilai + (float) ($pelayanan['nilai'] ?? $pelayananNilai));
                $totalHasilBobot = (float) ($birokrasiBobot + $pelayananBobot);
                $hasilPersen = $totalHasilBobot > 0 ? ($totalHasilNilai / $totalHasilBobot) * 100 : 0;

                $grandTotalBobot = $totalPengungkitBobot + $totalHasilBobot;
                $grandTotalNilai = $totalPengungkitNilai + $totalHasilNilai;
                $grandTotalPersen = $grandTotalBobot > 0 ? ($grandTotalNilai / $grandTotalBobot) * 100 : 0;

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

                $rekapRows->push([
                    'opd_id' => $opd->id,
                    'opd' => $opd->n_opd,
                    'areas' => $areaData,
                    'pengungkit' => [
                        'bobot' => $totalPengungkitBobot,
                        'nilai' => $totalPengungkitNilai,
                        'persen' => $pengungkitPersen,
                    ],
                    'birokrasi' => [
                        'bobot' => $birokrasiBobot,
                        'nilai' => $birokrasiNilai,
                        'persen' => $birokrasiPersen,
                    ],
                    'spak' => [
                        'bobot' => $spakBobot,
                        'nilai' => $spakNilai,
                        'persen' => $spakPersen,
                    ],
                    'capaian' => [
                        'bobot' => $capaianBobot,
                        'nilai' => $capaianNilai,
                        'persen' => $capaianPersen,
                    ],
                    'pelayanan' => [
                        'bobot' => $pelayananBobot,
                        'nilai' => $pelayananNilai,
                        'persen' => $pelayananPersen,
                    ],
                    'hasil' => [
                        'bobot' => $totalHasilBobot,
                        'nilai' => $totalHasilNilai,
                        'persen' => $hasilPersen,
                    ],
                    'total' => [
                        'bobot' => $grandTotalBobot,
                        'nilai' => $grandTotalNilai,
                        'persen' => $grandTotalPersen,
                    ],
                    'meets_wbk' => $meetsWbk,
                    'compliance' => $compliance,
                ]);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('page.verifikasi.rekap_dashboard_pdf', [
            'activePeriode' => $activePeriode,
            'role' => $role,
            'rekapRows' => $rekapRows,
            'areaOrder' => $areaOrder,
            'bobotMeta' => $bobotMeta,
            'thresholds' => $thresholds,
        ])->setPaper('a4', 'landscape');

        $roleTitle = $role == 'operator' ? 'Unit_Kerja' : ($role == 'verifikator' ? 'TPI' : 'TPE');

        return $pdf->download("Rekap_Evaluasi_ZI_{$roleTitle}_{$activePeriode->tahun}.pdf");
    }

    public function show(Periode $periode, Opd $opd)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $komponens = Komponen::with(['kategoris.subKategoris.indikators.pertanyaans.subPertanyaans'])
            ->orderBy('urutan')
            ->get();

        // Get all answers by this OPD to calculate progress
        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->with('files')
            ->get();

        $jawabanMap = [];
        $verifikasiStats = [
            'total_jawaban' => $jawabans->count(),
            'belum_diverifikasi' => $jawabans->where('status_verifikasi', 'belum_diverifikasi')->count(),
            'disetujui' => $jawabans->where('status_verifikasi', 'disetujui')->count(),
            'terkirim' => $jawabans->where('status_verifikasi', 'terkirim')->count(),
            'direvisi' => $jawabans->where('status_verifikasi', 'direvisi')->count(),
        ];

        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

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
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorVerifikasi($indikator, $jawabanMap);

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
        $verifikasiStats['disetujui'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'disetujui')->count();
        $verifikasiStats['terkirim'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'terkirim')->count();
        $verifikasiStats['direvisi'] = $jawabans->whereNull('sub_pertanyaan_id')->where('status_verifikasi', 'direvisi')->count();
        $verifikasiStats['belum_terverifikasi'] = max(0, $totalSemuaPertanyaan - $totalPertanyaanTerverifikasi - $verifikasiStats['direvisi']);

        $isAllAnswered = ($totalSemuaPertanyaan > 0 && $totalSemuaPertanyaan === $totalPertanyaanTerjawab);
        $isSent = $jawabans->where('status', 'final')->isNotEmpty();

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');
        $totalJawaban = (clone $opdBase)->count();
        $totalBelum = (clone $opdBase)->where('status_verifikasi', 'belum_diverifikasi')->count();
        $totalDirevisi = (clone $opdBase)->where('status_verifikasi', 'direvisi')->count();
        $totalTerkirim = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();
        $isReadySendMenpan = $totalJawaban > 0 && $totalBelum === 0 && $totalDirevisi === 0;
        $isSentToMenpan = $totalJawaban > 0 && $totalTerkirim === $totalJawaban;

        // Calculate WBK compliance for this OPD
        $progressSetForRekap = [];
        foreach ($progress as $subKategoriId => $prog) {
            $progressSetForRekap[$subKategoriId] = $prog['nilai'];
        }

        $areaOrder = [
            'MANAJEMEN PERUBAHAN',
            'PENATAAN TATALAKSANA',
            'PENATAAN SISTEM MANAJEMEN SDM APARATUR',
            'PENGUATAN AKUNTABILITAS',
            'PENGUATAN PENGAWASAN',
            'PENINGKATAN KUALITAS PELAYANAN PUBLIK',
        ];

        $rekap = $this->buildRekapFromProgress($progressSetForRekap, $komponens);
        $bobotMeta = $this->getRekapBobotMeta($komponens, $areaOrder);

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

        $birokrasiNilai = (float) ($birokrasi['nilai'] ?? 0);
        $spakNilai = (float) ($spak['nilai'] ?? 0);
        $capaianNilai = (float) ($capaian['nilai'] ?? 0);

        $pelayananSubs = collect($pelayanan['subs'] ?? []);
        $spp = $pelayananSubs->firstWhere('nama', 'Nilai Persepsi Kualitas Pelayanan (Survei Eksternal)')
            ?? ['nilai' => 0, 'bobot' => $bobotMeta['hasil']['pelayanan']];
        $pelayananNilai = (float) ($spp['nilai'] ?? $pelayanan['nilai'] ?? 0);

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

        return view('page.verifikasi.show', compact('periode', 'opd', 'komponens', 'jawabanMap', 'verifikasiStats', 'progress', 'isAllAnswered', 'isSent', 'isReadySendMenpan', 'isSentToMenpan', 'compliance', 'meetsWbk'));
    }

    public function detail(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $subKategori->load(['indikators.pertanyaans.subPertanyaans', 'kategori.komponen']);

        // Pagination indikator
        $indikators = $subKategori->indikators;
        $totalIndikator = $indikators->count();

        if ($totalIndikator == 0) {
            return redirect()->back()->with('error', 'Tidak ada indikator pada sub kategori ini.');
        }

        $currentPage = (int) max(1, min($request->get('indikator', 1), $totalIndikator));
        $currentIndikator = $indikators->get($currentPage - 1);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $nilaiIndikator = $this->hitungNilaiIndikatorVerifikasi($currentIndikator, $jawabanMap);

        // Cek apakah masih dalam masa verifikasi
        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        return view('page.verifikasi.detail', compact('periode', 'opd', 'subKategori', 'currentIndikator', 'currentPage', 'totalIndikator', 'jawabanMap', 'nilaiIndikator', 'isCanVerify', 'startVerif', 'endVerif'));
    }

    public function store(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Blokir penyimpanan jika di luar masa verifikasi
        $now = \Carbon\Carbon::now()->startOfDay();
        $startVerif = $periode->tanggal_mulai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_mulai_verifikasi)->startOfDay()
            : null;
        $endVerif = $periode->tanggal_selesai_verifikasi
            ? \Carbon\Carbon::parse($periode->tanggal_selesai_verifikasi)->endOfDay()
            : null;
        $isCanVerify = $startVerif && $endVerif && $now->between($startVerif, $endVerif);

        if (! $isCanVerify) {
            return redirect()->back()->with('error', 'Verifikasi tidak dapat dilakukan karena di luar masa waktu verifikasi.');
        }

        $verifikasiData = $request->input('verifikasi');
        $currentPage = $request->input('current_page', 1);

        if ($verifikasiData && is_array($verifikasiData)) {
            foreach ($verifikasiData as $pertanyaanId => $data) {
                // Update semua jawaban yang terkait dengan pertanyaan ini
                $jawabans = Jawaban::where('periode_id', $periode->id)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->whereNotIn('status_verifikasi', ['direvisi', 'terkirim'])
                    ->get();

                foreach ($jawabans as $jawaban) {
                    $jawaban->status_verifikasi = $data['status_verifikasi'] ?? 'belum_diverifikasi';

                    if (isset($data['catatan_verifikator'])) {
                        $jawaban->catatan_verifikator = $data['catatan_verifikator'];
                    }

                    if (isset($data['verifikator_jawaban_angka']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['verifikator_jawaban_angka'])) {
                        $val = $data['verifikator_jawaban_angka'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->verifikator_jawaban_angka = ($val !== null && $val !== '') ? $val : null;
                    }

                    if (isset($data['verifikator_jawaban_text']) && array_key_exists($jawaban->sub_pertanyaan_id ?: 0, $data['verifikator_jawaban_text'])) {
                        $val = $data['verifikator_jawaban_text'][$jawaban->sub_pertanyaan_id ?: 0];
                        $jawaban->verifikator_jawaban_text = ($val !== null && $val !== '') ? $val : null;
                    }

                    $verifikatorChanged = $jawaban->isDirty([
                        'status_verifikasi',
                        'verifikator_jawaban_text',
                        'verifikator_jawaban_angka',
                    ]);

                    if ($verifikatorChanged) {
                        $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
                        $jawaban->menpan_jawaban_text = null;
                        $jawaban->menpan_jawaban_angka = null;
                        $jawaban->menpan_verified_by = null;
                        $jawaban->menpan_verified_at = null;
                    }

                    if ($jawaban->status_verifikasi != 'belum_diverifikasi') {
                        $jawaban->verified_by = Auth::id();
                        $jawaban->verified_at = now();
                    } else {
                        $jawaban->verified_by = null;
                        $jawaban->verified_at = null;
                    }

                    $jawaban->save();
                }
            }
        }

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Data verifikasi berhasil disimpan.');
    }

    public function cancelPertanyaan(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'belum_diverifikasi';
            $jawaban->verified_by = null;
            $jawaban->verified_at = null;
            $jawaban->menunggu_dicek_ulang = false;
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Verifikasi pertanyaan berhasil dibatalkan.');
    }

    /**
     * Kirim permintaan revisi ke operator untuk satu pertanyaan.
     * Status menjadi 'direvisi', catatan verifikator disimpan, dan menunggu_dicek_ulang direset.
     */
    public function kirimRevisi(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string|max:2000',
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi sebelum mengirim permintaan revisi.',
        ]);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'direvisi';
            $jawaban->catatan_revisi = $request->input('catatan_revisi');
            $jawaban->verified_by = Auth::id();
            $jawaban->verified_at = now();
            $jawaban->menunggu_dicek_ulang = false; // operator belum merespons
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Permintaan revisi berhasil dikirim ke operator.');
    }

    /**
     * Batalkan permintaan revisi pertanyaan.
     * Status kembali menjadi 'belum_diverifikasi' dan catatan revisi dihapus.
     */
    public function cancelRevisi(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori, Pertanyaan $pertanyaan)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $hasTerkirim = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->where('status_verifikasi', 'terkirim')
            ->exists();

        if ($hasTerkirim) {
            return redirect()->back()->with('error', 'Jawaban sudah terkirim ke Menpan dan tidak bisa diubah.');
        }

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('pertanyaan_id', $pertanyaan->id)
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'belum_diverifikasi';
            $jawaban->catatan_revisi = null;
            $jawaban->verified_by = null;
            $jawaban->verified_at = null;
            $jawaban->menunggu_dicek_ulang = false;
            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;
            $jawaban->save();
        }

        $currentPage = $request->input('current_page', 1);

        return redirect()->route('verifikasi.detail', ['periode' => $periode->id, 'opd' => $opd->id, 'subKategori' => $subKategori->id, 'indikator' => $currentPage])
            ->with('success', 'Permintaan revisi berhasil dibatalkan.');
    }

    public function verifyAllDev(Request $request, Periode $periode, Opd $opd)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil semua jawaban OPD terkait di periode ini yang masih "belum_diverifikasi"
        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'belum_diverifikasi')
            ->get();

        foreach ($jawabans as $jawaban) {
            $jawaban->status_verifikasi = 'disetujui';

            // Set nilai verifikator agar tidak (Null) dengan menduplikat jawaban operator
            $jawaban->verifikator_jawaban_text = $jawaban->jawaban_text;
            $jawaban->verifikator_jawaban_angka = $jawaban->jawaban_angka;

            $jawaban->status_verifikasi_menpan = 'belum_diverifikasi';
            $jawaban->menpan_jawaban_text = null;
            $jawaban->menpan_jawaban_angka = null;
            $jawaban->menpan_verified_by = null;
            $jawaban->menpan_verified_at = null;

            $jawaban->verified_by = Auth::id();
            $jawaban->verified_at = now();
            $jawaban->save();
        }

        return redirect()->route('verifikasi.show', ['periode' => $periode->id, 'opd' => $opd->id])
            ->with('success', '[DEV] Semua kuesioner pada OPD ini telah berhasil diverifikasi secara otomatis.');
    }

    /**
     * Kirim hasil verifikasi ke Verifikator Menpan.
     */
    public function kirimMenpan(Request $request, Periode $periode, Opd $opd)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $opdBase = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->whereNull('sub_pertanyaan_id');

        $totalJawaban = (clone $opdBase)->count();
        $totalDisetujui = (clone $opdBase)->where('status_verifikasi', 'disetujui')->count();
        $totalTerkirim = (clone $opdBase)->where('status_verifikasi', 'terkirim')->count();

        if ($totalJawaban === 0) {
            return redirect()->back()->with('error', 'Belum ada jawaban yang dapat dikirim.');
        }

        if ($totalTerkirim === $totalJawaban) {
            return redirect()->back()->with('success', 'Hasil verifikasi sudah terkirim ke Menpan.');
        }

        if (($totalDisetujui + $totalTerkirim) < $totalJawaban) {
            return redirect()->back()->with('error', 'Masih ada jawaban yang belum disetujui.');
        }

        Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->where('status_verifikasi', 'disetujui')
            ->update(['status_verifikasi' => 'terkirim']);

        return redirect()->route('verifikasi.show', ['periode' => $periode->id, 'opd' => $opd->id])
            ->with('success', 'Hasil verifikasi berhasil dikirim ke Verifikator Menpan.');
    }

    private function buildProgressRekap($komponens, array $jawabanMap): array
    {
        $progress = [];

        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalNilaiSubKategori = 0;
                    foreach ($subKategori->indikators as $indikator) {
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorVerifikasi($indikator, $jawabanMap);
                        $totalNilaiSubKategori += $nilaiIndikatorData['nilai_indikator'];
                    }
                    $progress[$subKategori->id] = $totalNilaiSubKategori;
                }
            }
        }

        return $progress;
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

    private function hitungNilaiIndikatorVerifikasi(Indikator $indikator, array $jawabanMap): array
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
                    $key = $pertanyaan->id.'_'.$subPertanyaan->id;
                    $jawabanSubModel = $jawabanMap[$key] ?? null;
                    if ($jawabanSubModel) {
                        if (in_array($jawabanSubModel->status_verifikasi, ['disetujui', 'terkirim'], true)) {
                            $isVerified = true;
                        }
                        $value = $jawabanSubModel->verifikator_jawaban_angka;
                        if ($value === null || $value === '') {
                            $value = $jawabanSubModel->jawaban_angka;
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
                    if (in_array($jawaban->status_verifikasi, ['disetujui', 'terkirim'], true)) {
                        $isVerified = true;
                    }
                    if (in_array($pertanyaan->tipe_jawaban, ['ya_tidak', 'pilihan_ganda'])) {
                        $value = $jawaban->verifikator_jawaban_text ?? $jawaban->jawaban_text;
                    } else {
                        $value = $jawaban->verifikator_jawaban_angka ?? $jawaban->jawaban_angka;
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

        // Default: sub ke-1 = acuan (penyebut), sub ke-last = realisasi (pembilang)
        // Konsisten dengan KuesionerController
        $idAcuan = $subPertanyaans->first()->id;
        $idRealisasi = $subPertanyaans->last()->id; // default: b: pembilang (realisasi)

        // Penyesuaian untuk pertanyaan spesifik
        if (str_contains($pertanyaan->pertanyaan, 'Penurunan pelanggaran disiplin pegawai')) {
            $idRealisasi = $subPertanyaans->get(1)->id;
        } elseif (str_contains($pertanyaan->pertanyaan, 'Persentase penyampaian LHKPN')) {
            $idRealisasi = $subPertanyaans->where('urutan', 5)->first()->id ?? $subPertanyaans->last()->id;
        }

        $nilaiAcuan = floatval($jawabanSubArray[$idAcuan] ?? 0);
        $nilaiRealisasi = floatval($jawabanSubArray[$idRealisasi] ?? 0);

        if ($nilaiAcuan == 0 && $nilaiRealisasi == 0) {
            return 1.0;
        }

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

    public function exportLkePdf(Periode $periode, Opd $opd)
    {
        if (! in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators.pertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
                'kategoris.subKategoris.indikators.pertanyaans.subPertanyaans' => fn ($q) => $q->where('status', 1)->orderBy('urutan'),
            ])
            ->orderBy('urutan')
            ->get();

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->with('files')
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        $progress = [];
        $indikatorResults = [];
        foreach ($komponens as $komponen) {
            foreach ($komponen->kategoris as $kategori) {
                foreach ($kategori->subKategoris as $subKategori) {
                    $totalNilaiSubKategori = 0;
                    foreach ($subKategori->indikators as $indikator) {
                        $nilaiIndikatorData = $this->hitungNilaiIndikatorVerifikasi($indikator, $jawabanMap);
                        $totalNilaiSubKategori += $nilaiIndikatorData['nilai_indikator'];
                        $indikatorResults[$indikator->id] = $nilaiIndikatorData;
                    }
                    $progress[$subKategori->id] = $totalNilaiSubKategori;
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('page.verifikasi.lke_export_pdf', [
            'periode' => $periode,
            'opd' => $opd,
            'komponens' => $komponens,
            'jawabanMap' => $jawabanMap,
            'progress' => $progress,
            'indikatorResults' => $indikatorResults,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Detail_LKE_'.str_replace(' ', '_', $opd->n_opd)."_{$periode->tahun}.pdf");
    }
}
