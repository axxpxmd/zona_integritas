<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jawaban;
use App\Models\JawabanFile;
use App\Models\Kategori;
use App\Models\Komponen;
use App\Models\Opd;
use App\Models\Periode;
use App\Models\Pertanyaan;
use App\Models\SubKategori;
use App\Models\SubPertanyaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the CMS dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $roleLabels = [
            'admin' => 'Administrator',
            'operator' => 'Operator',
            'verifikator' => 'Verifikator',
            'verifikator_menhan' => 'Verifikator Menhan',
        ];

        $displayName = $user->nama_operator
            ?? $user->nama_kepala
            ?? $user->nama_instansi
            ?? $user->username;

        // Ambil periode aktif
        $activePeriode = Periode::aktif()->first();

        // Hitung total pertanyaan wajib yang harus dijawab
        $totalRequired = 0;
        $opdProgress = collect();
        $totalOpd = Opd::where('status', 1)->count();
        $opdCompleted = 0;
        $opdInProgress = 0;
        $opdNotStarted = 0;

        if ($activePeriode) {
            $totalPertanyaan = Pertanyaan::where('status', 1)->doesntHave('subPertanyaans')->count();
            $totalSubPertanyaan = SubPertanyaan::where('status', 1)->whereHas('pertanyaanUtama', function ($q) {
                $q->where('status', 1);
            })->count();
            $totalRequired = $totalPertanyaan + $totalSubPertanyaan;

            $opds = Opd::where('status', 1)->get();

            foreach ($opds as $opd) {
                // Hintung jumlah jawaban unik berdasarkan opd
                $jwbnCount = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->count();

                // Pastikan tidak melebihi 100% jika ada data bermasalah
                $progressPercent = $totalRequired > 0 ? min(100, round(($jwbnCount / $totalRequired) * 100)) : 0;

                if ($progressPercent == 100) {
                    $statusName = 'Selesai';
                    $color = 'green';
                    $opdCompleted++;
                } elseif ($progressPercent > 0) {
                    $statusName = 'Dalam Proses';
                    $color = 'yellow';
                    $opdInProgress++;
                } else {
                    $statusName = 'Belum Mengisi';
                    $color = 'gray';
                    $opdNotStarted++;
                }

                $opdProgress->push((object) [
                    'opd' => $opd,
                    'terisi' => $jwbnCount,
                    'total' => $totalRequired,
                    'persentase' => $progressPercent,
                    'status' => $statusName,
                    'color' => $color
                ]);
            }

            // Urutkan berdasarkan persentase tertinggi
            $opdProgress = $opdProgress->sortByDesc('persentase')->values();
        } else {
            $opdNotStarted = $totalOpd;
        }

        // Hitung stats kuesioner khusus operator
        $operatorStats = [];
        if ($user->role === 'operator' && $user->opd) {
            $operatorStats = $this->getOperatorStats($activePeriode, $user->opd);
        }

        // Hitung stats verifikasi khusus verifikator/admin
        $verifikatorStats = [];
        if ($user->role === 'verifikator') {
            $verifikatorStats = $this->getVerifikatorStats($activePeriode, $user);
        }

        // Hitung stats verifikasi khusus verifikator menhan/admin
        $menhanStats = [];
        if ($user->role === 'verifikator_menhan') {
            $menhanStats = $this->getVerifikatorMenhanStats($activePeriode, $user);
        }

        return view('page.dashboard', [
            'displayName' => $displayName,
            'username' => $user->username,
            'roleLabel' => $roleLabels[$user->role] ?? ucfirst($user->role),
            'activePeriode' => $activePeriode,
            'totalOpd' => $totalOpd,
            'opdCompleted' => $opdCompleted,
            'opdInProgress' => $opdInProgress,
            'opdNotStarted' => $opdNotStarted,
            'opdProgress' => $opdProgress,
            'totalRequired' => $totalRequired,
            // Operator-specific stats
            'operatorStats' => $operatorStats,
            // Verifikator-specific stats
            'verifikatorStats' => $verifikatorStats,
            // Verifikator Menhan-specific stats
            'menhanStats' => $menhanStats,
        ]);
    }

    /**
     * Hitung statistik kuesioner untuk operator yang sedang login.
     */
    private function getOperatorStats($activePeriode, $opd): array
    {
        if (!$activePeriode || !$opd) {
            return [];
        }

        $periodeId = $activePeriode->id;
        $opdId = $opd->id;
        $now = Carbon::now()->startOfDay();

        // --- Total pertanyaan wajib (hanya pertanyaan utama, tidak termasuk sub-pertanyaan) ---
        $totalRequired = Pertanyaan::where('status', 1)->count();

        // --- Progress pengisian (hanya jawaban utama) ---
        $totalDiisi = Jawaban::where('periode_id', $periodeId)
            ->where('opd_id', $opdId)
            ->whereNull('sub_pertanyaan_id')
            ->count();
        $persenPengisian = $totalRequired > 0 ? min(100, round(($totalDiisi / $totalRequired) * 100)) : 0;

        // --- Status kirim ke verifikator ---
        $isKirimFinal = Jawaban::where('periode_id', $periodeId)
            ->where('opd_id', $opdId)
            ->where('status', 'final')
            ->exists();

        // --- Verifikasi summary (hanya jawaban utama, bukan sub) ---
        $jawabansMainQuery = Jawaban::where('periode_id', $periodeId)
            ->where('opd_id', $opdId)
            ->whereNull('sub_pertanyaan_id');

        $totalDisetujui = (clone $jawabansMainQuery)
            ->whereIn('status_verifikasi', ['disetujui', 'terkirim'])
            ->count();
        $totalDirevisi = (clone $jawabansMainQuery)
            ->where('status_verifikasi', 'direvisi')
            ->count();
        $totalBelumDiverifikasi = (clone $jawabansMainQuery)
            ->where('status_verifikasi', 'belum_diverifikasi')
            ->count();

        // --- Menunggu dicek ulang (revisi sudah dikirim operator, belum dicek verifikator) ---
        $totalMenungguDicekUlang = (clone $jawabansMainQuery)
            ->where('menunggu_dicek_ulang', true)
            ->count();

        // --- Total dokumen yang diupload ---
        $totalDokumen = JawabanFile::whereHas('jawaban', function ($q) use ($periodeId, $opdId) {
            $q->where('periode_id', $periodeId)->where('opd_id', $opdId);
        })->count();

        // --- Status masa pengisian ---
        $start = Carbon::parse($activePeriode->tanggal_mulai)->startOfDay();
        $end = Carbon::parse($activePeriode->tanggal_selesai)->endOfDay();
        $isCanFill = $now->between($start, $end);

        // --- Status masa revisi ---
        $startRevisi = $activePeriode->tanggal_mulai_revisi
            ? Carbon::parse($activePeriode->tanggal_mulai_revisi)->startOfDay() : null;
        $endRevisi = $activePeriode->tanggal_selesai_revisi
            ? Carbon::parse($activePeriode->tanggal_selesai_revisi)->endOfDay() : null;
        $isCanRevisi = ($startRevisi && $endRevisi) ? $now->between($startRevisi, $endRevisi) : false;

        // --- Status masa verifikasi (untuk info saja) ---
        $startVerif = $activePeriode->tanggal_mulai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_mulai_verifikasi)->startOfDay() : null;
        $endVerif = $activePeriode->tanggal_selesai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_selesai_verifikasi)->endOfDay() : null;
        $isVerifActive = ($startVerif && $endVerif) ? $now->between($startVerif, $endVerif) : false;

        // Determine overall kuesioner status label
        if ($isKirimFinal && $totalDisetujui === $totalBelumDiverifikasi + $totalDisetujui + $totalDirevisi && $totalDirevisi === 0) {
            $statusLabel = 'Selesai Diverifikasi';
            $statusColor = 'green';
        } elseif ($totalDirevisi > 0) {
            $statusLabel = 'Ada Revisi';
            $statusColor = 'red';
        } elseif ($isKirimFinal) {
            $statusLabel = 'Sudah Dikirim';
            $statusColor = 'blue';
        } elseif ($persenPengisian === 100) {
            $statusLabel = 'Siap Dikirim';
            $statusColor = 'yellow';
        } elseif ($persenPengisian > 0) {
            $statusLabel = 'Sedang Diisi';
            $statusColor = 'indigo';
        } else {
            $statusLabel = 'Belum Dimulai';
            $statusColor = 'gray';
        }

        return [
            'totalRequired' => $totalRequired,
            'totalDiisi' => $totalDiisi,
            'persenPengisian' => $persenPengisian,
            'isKirimFinal' => $isKirimFinal,
            'totalDisetujui' => $totalDisetujui,
            'totalDirevisi' => $totalDirevisi,
            'totalBelumDiverifikasi' => $totalBelumDiverifikasi,
            'totalMenungguDicekUlang' => $totalMenungguDicekUlang,
            'totalDokumen' => $totalDokumen,
            'isCanFill' => $isCanFill,
            'isCanRevisi' => $isCanRevisi,
            'isVerifActive' => $isVerifActive,
            'statusLabel' => $statusLabel,
            'statusColor' => $statusColor,
            'tanggalMulai' => $start,
            'tanggalSelesai' => $end,
            'tanggalMulaiRevisi' => $startRevisi,
            'tanggalSelesaiRevisi' => $endRevisi,
        ];
    }

    /**
     * Hitung statistik verifikasi untuk verifikator yang sedang login.
     * Verifikator hanya melihat OPD yang di-assign kepadanya via opd_verifikator.
     */
    private function getVerifikatorStats($activePeriode, $user): array
    {
        if (!$activePeriode) {
            return [];
        }

        $periodeId = $activePeriode->id;
        $now = Carbon::now()->startOfDay();

        $totalRequired = \App\Models\Pertanyaan::where('status', 1)->count();

        // OPD yang di-assign ke verifikator ini (atau semua OPD jika admin)
        if ($user->role === 'admin') {
            $assignedOpdIds = Opd::pluck('id');
        } else {
            $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                ->where('user_id', $user->id)
                ->pluck('opd_id');
        }

        $totalOpdAssigned = $assignedOpdIds->count();

        // OPD yang sudah mengirimkan kuesioner (status final)
        $opdSudahKirim = Jawaban::where('periode_id', $periodeId)
            ->where('status', 'final')
            ->whereIn('opd_id', $assignedOpdIds)
            ->distinct('opd_id')
            ->count('opd_id');

        $opdBelumKirim = $totalOpdAssigned - $opdSudahKirim;

        // Verifikasi summary (hanya jawaban utama) di semua OPD yang di-assign
        $baseQuery = Jawaban::where('periode_id', $periodeId)
            ->whereIn('opd_id', $assignedOpdIds)
            ->whereNull('sub_pertanyaan_id');

        $totalDisetujui = (clone $baseQuery)->whereIn('status_verifikasi', ['disetujui', 'terkirim'])->count();
        $totalDirevisi = (clone $baseQuery)->where('status_verifikasi', 'direvisi')->count();
        $totalBelumDiverifikasi = (clone $baseQuery)->where('status_verifikasi', 'belum_diverifikasi')->count();
        $totalMenungguDicekUlang = (clone $baseQuery)->where('menunggu_dicek_ulang', true)->count();
        $totalJawaban = (clone $baseQuery)->count();

        // Progress verifikasi keseluruhan (disetujui + direvisi = sudah ditindaklanjuti)
        $persenVerifikasi = $totalJawaban > 0
            ? min(100, round((($totalDisetujui + $totalDirevisi) / $totalJawaban) * 100))
            : 0;

        // Per-OPD progress untuk tabel ringkasan
        $opdProgressVerif = collect();
        if ($assignedOpdIds->isNotEmpty()) {
            $opds = Opd::whereIn('id', $assignedOpdIds)->get();
            foreach ($opds as $opd) {
                $isFinal = Jawaban::where('periode_id', $periodeId)
                    ->where('opd_id', $opd->id)
                    ->where('status', 'final')
                    ->exists();

                $opd_base = Jawaban::where('periode_id', $periodeId)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $jmlDisetujui = (clone $opd_base)->whereIn('status_verifikasi', ['disetujui', 'terkirim'])->count();
                $jmlDirevisi = (clone $opd_base)->where('status_verifikasi', 'direvisi')->count();
                $jmlBelum = (clone $opd_base)->where('status_verifikasi', 'belum_diverifikasi')->count();
                $jmlTotal = (clone $opd_base)->count();
                $jmlMenunggu = (clone $opd_base)->where('menunggu_dicek_ulang', true)->count();

                $persen = $jmlTotal > 0
                    ? min(100, round((($jmlDisetujui + $jmlDirevisi) / $jmlTotal) * 100))
                    : 0;

                $opdProgressVerif->push((object) [
                    'opd' => $opd,
                    'isFinal' => $isFinal,
                    'disetujui' => $jmlDisetujui,
                    'direvisi' => $jmlDirevisi,
                    'belum' => $jmlBelum,
                    'total' => $jmlTotal,
                    'menunggu' => $jmlMenunggu,
                    'persen' => $persen,
                    'totalDiisi' => $jmlTotal,
                    'totalRequired' => $totalRequired,
                ]);
            }
            $opdProgressVerif = $opdProgressVerif->sortByDesc('isFinal')->sortByDesc('persen')->values();
        }

        // Jadwal verifikasi
        $startVerif = $activePeriode->tanggal_mulai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_mulai_verifikasi)->startOfDay() : null;
        $endVerif = $activePeriode->tanggal_selesai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_selesai_verifikasi)->endOfDay() : null;
        $isVerifActive = ($startVerif && $endVerif) ? $now->between($startVerif, $endVerif) : false;

        return [
            'totalOpdAssigned' => $totalOpdAssigned,
            'opdSudahKirim' => $opdSudahKirim,
            'opdBelumKirim' => $opdBelumKirim,
            'totalDisetujui' => $totalDisetujui,
            'totalDirevisi' => $totalDirevisi,
            'totalBelumDiverifikasi' => $totalBelumDiverifikasi,
            'totalMenungguDicekUlang' => $totalMenungguDicekUlang,
            'totalJawaban' => $totalJawaban,
            'persenVerifikasi' => $persenVerifikasi,
            'opdProgressVerif' => $opdProgressVerif,
            'isVerifActive' => $isVerifActive,
            'startVerif' => $startVerif,
            'endVerif' => $endVerif,
        ];
    }

    /**
     * Hitung statistik verifikasi untuk verifikator menhan.
     * Hanya menilai jawaban yang sudah disetujui oleh verifikator biasa.
     */
    private function getVerifikatorMenhanStats($activePeriode, $user): array
    {
        if (!$activePeriode) {
            return [];
        }

        $periodeId = $activePeriode->id;
        $now = Carbon::now()->startOfDay();

        if ($user->role === 'admin') {
            $assignedOpdIds = Opd::pluck('id');
        } else {
            $assignedOpdIds = \Illuminate\Support\Facades\DB::table('opd_verifikator')
                ->where('user_id', $user->id)
                ->pluck('opd_id');
        }

        $totalOpdAssigned = $assignedOpdIds->count();

        $opdSiapMenhan = 0;
        $opdBelumSiapMenhan = 0;

        $opdProgressMenhan = collect();

        if ($assignedOpdIds->isNotEmpty()) {
            $opds = Opd::whereIn('id', $assignedOpdIds)->get();
            foreach ($opds as $opd) {
                $opd_base = Jawaban::where('periode_id', $periodeId)
                    ->where('opd_id', $opd->id)
                    ->whereNull('sub_pertanyaan_id');

                $jmlTotal = (clone $opd_base)->count();
                if ($jmlTotal === 0) {
                    $opdBelumSiapMenhan++;
                    continue;
                }

                $jmlDisetujuiVerifikator = (clone $opd_base)->where('status_verifikasi', 'terkirim')->count();
                $isSiap = $jmlDisetujuiVerifikator === $jmlTotal;

                if ($isSiap) {
                    $opdSiapMenhan++;
                } else {
                    $opdBelumSiapMenhan++;
                }

                $jmlDisetujuiMenhan = (clone $opd_base)->where('status_verifikasi_menhan', 'disetujui')->count();
                $jmlBelumMenhan = (clone $opd_base)->where('status_verifikasi_menhan', 'belum_diverifikasi')->count();

                $persen = $jmlTotal > 0
                    ? min(100, round(($jmlDisetujuiMenhan / $jmlTotal) * 100))
                    : 0;

                $opdProgressMenhan->push((object) [
                    'opd' => $opd,
                    'isSiap' => $isSiap,
                    'disetujui' => $jmlDisetujuiMenhan,
                    'belum' => $jmlBelumMenhan,
                    'total' => $jmlTotal,
                    'persen' => $persen,
                ]);
            }

            $opdProgressMenhan = $opdProgressMenhan->sortByDesc('isSiap')->sortByDesc('persen')->values();
        }

        $baseQuery = Jawaban::where('periode_id', $periodeId)
            ->whereNull('sub_pertanyaan_id')
            ->where('status_verifikasi', 'terkirim');

        $totalDisetujui = (clone $baseQuery)->where('status_verifikasi_menhan', 'disetujui')->count();
        $totalBelumDiverifikasi = (clone $baseQuery)->where('status_verifikasi_menhan', 'belum_diverifikasi')->count();
        $totalJawaban = (clone $baseQuery)->count();

        $persenVerifikasi = $totalJawaban > 0
            ? min(100, round(($totalDisetujui / $totalJawaban) * 100))
            : 0;

        // Jadwal verifikasi (gunakan jadwal verifikasi umum)
        $startVerif = $activePeriode->tanggal_mulai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_mulai_verifikasi)->startOfDay() : null;
        $endVerif = $activePeriode->tanggal_selesai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_selesai_verifikasi)->endOfDay() : null;
        $isVerifActive = ($startVerif && $endVerif) ? $now->between($startVerif, $endVerif) : false;

        return [
            'totalOpdAssigned' => $totalOpdAssigned,
            'opdSiapMenhan' => $opdSiapMenhan,
            'opdBelumSiapMenhan' => $opdBelumSiapMenhan,
            'totalDisetujui' => $totalDisetujui,
            'totalBelumDiverifikasi' => $totalBelumDiverifikasi,
            'totalJawaban' => $totalJawaban,
            'persenVerifikasi' => $persenVerifikasi,
            'opdProgressMenhan' => $opdProgressMenhan,
            'isVerifActive' => $isVerifActive,
            'startVerif' => $startVerif,
            'endVerif' => $endVerif,
        ];
    }
}

