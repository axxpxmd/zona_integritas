<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jawaban;
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
            $totalSubPertanyaan = SubPertanyaan::where('status', 1)->whereHas('pertanyaanUtama', function($q) {
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

                $opdProgress->push((object)[
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
            'totalRequired' => $totalRequired
        ]);
    }
}
