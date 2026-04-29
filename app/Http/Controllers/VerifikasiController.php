<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Opd;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
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
            $opdIds = Jawaban::where('periode_id', $activePeriode->id)
                ->where('status', 'final')
                ->distinct()
                ->pluck('opd_id');

            $submittedOpds = Opd::whereIn('id', $opdIds)->get();

            // For each OPD, we can also get the date they submitted (max updated_at where status=final)
            foreach ($submittedOpds as $opd) {
                $lastSubmit = Jawaban::where('periode_id', $activePeriode->id)
                    ->where('opd_id', $opd->id)
                    ->where('status', 'final')
                    ->max('updated_at');

                $opd->submitted_at = $lastSubmit;
            }
        }

        return view('page.verifikasi.index', compact('periodes', 'activePeriode', 'submittedOpds'));
    }
}
