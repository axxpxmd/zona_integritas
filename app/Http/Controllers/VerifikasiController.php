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
            ->with('files')
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
                        $indPertanyaanTerjawab = 0;
                        $indTotalNilai = 0;

                        foreach ($indikator->pertanyaans as $pertanyaan) {
                            $totalPertanyaan++;
                            $totalSemuaPertanyaan++;

                            $jawaban = $jawabansParent[$pertanyaan->id] ?? null;

                            if ($jawaban) {
                                $pertanyaanTerjawab++;
                                $totalPertanyaanTerjawab++;

                                if ($jawaban->status_verifikasi !== 'belum_diverifikasi') {
                                    $pertanyaanTerverifikasi++;
                                    $totalPertanyaanTerverifikasi++;
                                }

                                if ($jawaban->nilai !== null) {
                                    $indPertanyaanTerjawab++;
                                    $indTotalNilai += $jawaban->nilai;
                                }
                            }
                        }

                        $indRataRata = $indPertanyaanTerjawab > 0 ? $indTotalNilai / $indPertanyaanTerjawab : 0;
                        $indNilaiAkhir = $indRataRata * $indikator->bobot;
                        $totalNilaiSubKategori += $indNilaiAkhir;
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

        return view('page.verifikasi.show', compact('periode', 'opd', 'komponens', 'jawabanMap', 'verifikasiStats', 'progress', 'isAllAnswered', 'isSent'));
    }

    public function detail(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
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

        return view('page.verifikasi.detail', compact('periode', 'opd', 'subKategori', 'currentIndikator', 'currentPage', 'totalIndikator', 'jawabanMap'));
    }

    public function store(Request $request, Periode $periode, Opd $opd, SubKategori $subKategori)
    {
        if (!in_array(Auth::user()->role, ['admin', 'verifikator'])) {
            abort(403, 'Akses ditolak.');
        }

        $verifikasiData = $request->input('verifikasi');
        $currentPage = $request->input('current_page', 1);

        if ($verifikasiData && is_array($verifikasiData)) {
            foreach ($verifikasiData as $pertanyaanId => $data) {
                // Update semua jawaban yang terkait dengan pertanyaan ini
                $jawabans = Jawaban::where('periode_id', $periode->id)
                    ->where('opd_id', $opd->id)
                    ->where('pertanyaan_id', $pertanyaanId)
                    ->get();

                foreach ($jawabans as $jawaban) {
                    $jawaban->status_verifikasi = $data['status_verifikasi'] ?? 'belum_diverifikasi';

                    if (isset($data['catatan_verifikator'])) {
                        $jawaban->catatan_verifikator = $data['catatan_verifikator'];
                    }

                    if (isset($data['verifikator_jawaban_angka'][$jawaban->sub_pertanyaan_id ?: 0])) {
                         $jawaban->verifikator_jawaban_angka = $data['verifikator_jawaban_angka'][$jawaban->sub_pertanyaan_id ?: 0];
                    }

                    if (isset($data['verifikator_jawaban_text'][$jawaban->sub_pertanyaan_id ?: 0])) {
                         $jawaban->verifikator_jawaban_text = $data['verifikator_jawaban_text'][$jawaban->sub_pertanyaan_id ?: 0];
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
            ->with('success', 'Data verifikasi untuk indikator ini berhasil disimpan.');
    }
}
