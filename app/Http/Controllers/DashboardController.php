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

        return view('page.dashboard', [
            'displayName'    => $displayName,
            'username'       => $user->username,
            'roleLabel'      => $roleLabels[$user->role] ?? ucfirst($user->role),
            'activePeriode'  => $activePeriode,
            'totalOpd'       => $totalOpd,
            'opdCompleted'   => $opdCompleted,
            'opdInProgress'  => $opdInProgress,
            'opdNotStarted'  => $opdNotStarted,
            'opdProgress'    => $opdProgress,
            'totalRequired'  => $totalRequired,
            // Operator-specific stats
            'operatorStats'  => $operatorStats,
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
        $opdId     = $opd->id;
        $now       = Carbon::now()->startOfDay();

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
            ->where('status_verifikasi', 'disetujui')
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
        $start       = Carbon::parse($activePeriode->tanggal_mulai)->startOfDay();
        $end         = Carbon::parse($activePeriode->tanggal_selesai)->endOfDay();
        $isCanFill   = $now->between($start, $end);

        // --- Status masa revisi ---
        $startRevisi = $activePeriode->tanggal_mulai_revisi
            ? Carbon::parse($activePeriode->tanggal_mulai_revisi)->startOfDay() : null;
        $endRevisi   = $activePeriode->tanggal_selesai_revisi
            ? Carbon::parse($activePeriode->tanggal_selesai_revisi)->endOfDay() : null;
        $isCanRevisi = ($startRevisi && $endRevisi) ? $now->between($startRevisi, $endRevisi) : false;

        // --- Status masa verifikasi (untuk info saja) ---
        $startVerif = $activePeriode->tanggal_mulai_verifikasi
            ? Carbon::parse($activePeriode->tanggal_mulai_verifikasi)->startOfDay() : null;
        $endVerif   = $activePeriode->tanggal_selesai_verifikasi
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
            'totalRequired'          => $totalRequired,
            'totalDiisi'             => $totalDiisi,
            'persenPengisian'        => $persenPengisian,
            'isKirimFinal'           => $isKirimFinal,
            'totalDisetujui'         => $totalDisetujui,
            'totalDirevisi'          => $totalDirevisi,
            'totalBelumDiverifikasi' => $totalBelumDiverifikasi,
            'totalMenungguDicekUlang'=> $totalMenungguDicekUlang,
            'totalDokumen'           => $totalDokumen,
            'isCanFill'              => $isCanFill,
            'isCanRevisi'            => $isCanRevisi,
            'isVerifActive'          => $isVerifActive,
            'statusLabel'            => $statusLabel,
            'statusColor'            => $statusColor,
            'tanggalMulai'           => $start,
            'tanggalSelesai'         => $end,
            'tanggalMulaiRevisi'     => $startRevisi,
            'tanggalSelesaiRevisi'   => $endRevisi,
        ];
    }
}
