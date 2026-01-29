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

        // Statistik untuk Admin
        if ($user->role === 'admin') {
            $stats = [
                'periode' => Periode::count(),
                'periodeAktif' => Periode::where('status', 1)->count(),
                'komponen' => Komponen::count(),
                'komponenAktif' => Komponen::where('status', 1)->count(),
                'kategori' => Kategori::count(),
                'kategoriAktif' => Kategori::where('status', 1)->count(),
                'subKategori' => SubKategori::count(),
                'subKategoriAktif' => SubKategori::where('status', 1)->count(),
                'indikator' => Indikator::count(),
                'indikatorAktif' => Indikator::where('status', 1)->count(),
                'pertanyaan' => Pertanyaan::count(),
                'pertanyaanAktif' => Pertanyaan::where('status', 1)->count(),
                'subPertanyaan' => SubPertanyaan::count(),
                'subPertanyaanAktif' => SubPertanyaan::where('status', 1)->count(),
                'opd' => Opd::count(),
                'opdAktif' => Opd::where('status', 1)->count(),
                'user' => User::count(),
                'jawaban' => Jawaban::count(),
            ];
        } else {
            // Statistik untuk Operator/Verifikator
            $stats = [
                'periodeAktif' => Periode::where('status', 1)->count(),
                'pertanyaanTotal' => Pertanyaan::where('status', 1)->count(),
                'subPertanyaanTotal' => SubPertanyaan::where('status', 1)->count(),
                'jawabanSaya' => Jawaban::where('opd_id', $user->opd_id)->count(),
            ];
        }

        return view('page.dashboard', compact('stats'));
    }
}
