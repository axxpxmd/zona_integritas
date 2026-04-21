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

        return view('page.dashboard', [
            'displayName' => $displayName,
            'username' => $user->username,
            'roleLabel' => $roleLabels[$user->role] ?? ucfirst($user->role),
        ]);
    }
}
