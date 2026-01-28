<?php

namespace App\Http\Controllers;

use App\Models\{Periode, Komponen, Kategori, SubKategori, Indikator, Pertanyaan, Jawaban};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KuesionerController extends Controller
{
    /**
     * Halaman pilih periode
     */
    public function index()
    {
        // Ambil periode yang aktif atau berlangsung
        $periodes = Periode::whereIn('status', ['aktif', 'selesai'])
            ->where('is_template', false)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('page.kuesioner.index', compact('periodes'));
    }

    /**
     * Halaman kuesioner berdasarkan periode yang dipilih
     */
    public function show($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        // Ambil OPD user yang login
        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return redirect()->route('kuesioner.index')
                ->with('error', 'User Anda belum terhubung dengan OPD');
        }

        // Ambil struktur kuesioner: Komponen → Kategori → SubKategori → Indikator → Pertanyaan
        $komponens = Komponen::where('status', 1)
            ->with([
                'kategoris' => function($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris' => function($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators' => function($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators.pertanyaans' => function($q) {
                    $q->where('status', 1)->orderBy('urutan');
                },
                'kategoris.subKategoris.indikators.pertanyaans.subPertanyaans' => function($q) {
                    $q->orderBy('urutan');
                }
            ])
            ->orderBy('urutan')
            ->get();

        // Ambil jawaban yang sudah diisi oleh OPD ini untuk periode ini
        $jawabans = Jawaban::where('periode_id', $periode_id)
            ->where('opd_id', $opd->id)
            ->get()
            ->keyBy(function($item) {
                // Key: pertanyaan_id atau pertanyaan_id-sub_pertanyaan_id
                return $item->sub_pertanyaan_id
                    ? $item->pertanyaan_id . '-' . $item->sub_pertanyaan_id
                    : $item->pertanyaan_id;
            });

        return view('page.kuesioner.form', compact('periode', 'opd', 'komponens', 'jawabans'));
    }

    /**
     * Auto-save jawaban (AJAX)
     */
    public function autoSave(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:tm_periode,id',
            'pertanyaan_id' => 'required|exists:tm_pertanyaan,id',
            'sub_pertanyaan_id' => 'nullable|exists:tm_sub_pertanyaan,id',
            'jawaban_text' => 'nullable|string',
            'jawaban_angka' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $opd = $user->opd;

        if (!$opd) {
            return response()->json(['success' => false, 'message' => 'OPD tidak ditemukan'], 400);
        }

        // Update or create jawaban
        $jawaban = Jawaban::updateOrCreate(
            [
                'periode_id' => $validated['periode_id'],
                'opd_id' => $opd->id,
                'pertanyaan_id' => $validated['pertanyaan_id'],
                'sub_pertanyaan_id' => $validated['sub_pertanyaan_id'] ?? null,
            ],
            [
                'jawaban_text' => $validated['jawaban_text'] ?? null,
                'jawaban_angka' => $validated['jawaban_angka'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => 'draft',
                'created_by' => $jawaban->created_by ?? $user->id,
                'updated_by' => $user->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'data' => $jawaban
        ]);
    }
}
