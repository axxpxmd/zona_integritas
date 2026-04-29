<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Opd;
use App\Models\Komponen;
use App\Models\SubKategori;
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

    public function show(Periode $periode, Opd $opd)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $komponens = Komponen::with(['kategoris.subKategoris.indikators.pertanyaans.subPertanyaans'])
            ->orderBy('urutan')
            ->get();

        // Get all answers by this OPD to calculate progress
        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->get();

        $jawabanMap = [];
        $verifikasiStats = [
            'total_jawaban' => $jawabans->count(),
            'belum_diverifikasi' => $jawabans->where('status_verifikasi', 'belum_diverifikasi')->count(),
            'disetujui' => $jawabans->where('status_verifikasi', 'disetujui')->count(),
            'direvisi' => $jawabans->where('status_verifikasi', 'direvisi')->count(),
        ];

        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        return view('page.verifikasi.show', compact('periode', 'opd', 'komponens', 'jawabanMap', 'verifikasiStats'));
    }

    public function detail(Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $subKategori->load(['indikators.pertanyaans.subPertanyaans', 'kategori.komponen']);

        $jawabans = Jawaban::where('periode_id', $periode->id)
            ->where('opd_id', $opd->id)
            ->get();

        $jawabanMap = [];
        foreach ($jawabans as $j) {
            $key = $j->sub_pertanyaan_id ? "{$j->pertanyaan_id}_{$j->sub_pertanyaan_id}" : $j->pertanyaan_id;
            $jawabanMap[$key] = $j;
        }

        return view('page.verifikasi.detail', compact('periode', 'opd', 'subKategori', 'jawabanMap'));
    }
}
