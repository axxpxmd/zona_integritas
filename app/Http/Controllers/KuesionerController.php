<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use App\Models\Indikator;
use Illuminate\Http\Request;

class KuesionerController extends Controller
{
    /**
     * Display kuesioner form untuk indikator tertentu
     */
    public function show($indikator_id)
    {
        $indikator = Indikator::findOrFail($indikator_id);

        // Ambil pertanyaan untuk indikator ini, urutkan by urutan
        $pertanyaans = Pertanyaan::where('indikator_id', $indikator_id)
            ->where('status', 1)
            ->orderBy('urutan')
            ->get();

        return view('kuesioner.form-example', [
            'indikator' => $indikator,
            'pertanyaans' => $pertanyaans
        ]);
    }

    /**
     * Process submitted kuesioner
     */
    public function submit(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|string',
        ], [
            'jawaban.required' => 'Harap jawab semua pertanyaan',
            'jawaban.*.required' => 'Harap jawab pertanyaan ini',
        ]);

        // Process jawaban...
        // Simpan ke database, hitung skor, dll

        return redirect()
            ->route('kuesioner.result')
            ->with('success', 'Kuesioner berhasil disimpan');
    }

    /**
     * Demo: Tampilkan semua pertanyaan dengan parsing
     */
    public function demo()
    {
        $pertanyaans = Pertanyaan::where('tipe_jawaban', 'pilihan_ganda')
            ->where('status', 1)
            ->take(10)
            ->get();

        return view('kuesioner.demo', compact('pertanyaans'));
    }
}
