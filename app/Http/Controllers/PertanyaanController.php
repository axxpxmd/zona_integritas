<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kategori;
use App\Models\Komponen;
use App\Models\Pertanyaan;
use App\Models\SubKategori;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pertanyaan::with(['indikator.subKategori.kategori.komponen']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('penjelasan', 'like', "%{$search}%")
                  ->orWhereHas('indikator', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Komponen
        if ($request->filled('komponen_id')) {
            $query->whereHas('indikator.subKategori.kategori', function ($q) use ($request) {
                $q->where('komponen_id', $request->komponen_id);
            });
        }

        // Filter by Kategori
        if ($request->filled('kategori_id')) {
            $query->whereHas('indikator.subKategori', function ($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter by Sub Kategori
        if ($request->filled('sub_kategori_id')) {
            $query->whereHas('indikator', function ($q) use ($request) {
                $q->where('sub_kategori_id', $request->sub_kategori_id);
            });
        }

        // Filter by Indikator
        if ($request->filled('indikator_id')) {
            $query->where('indikator_id', $request->indikator_id);
        }

        // Filter by Tipe Jawaban
        if ($request->filled('tipe_jawaban')) {
            $query->where('tipe_jawaban', $request->tipe_jawaban);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pertanyaans = $query->orderBy('urutan')->paginate(10)->withQueryString();

        // Stats
        $total = Pertanyaan::count();
        $aktif = Pertanyaan::where('status', 1)->count();
        $tidakAktif = Pertanyaan::where('status', 0)->count();

        // Dropdowns for filters
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();
        $subKategoris = SubKategori::with('kategori.komponen')->where('status', 1)->orderBy('urutan')->get();
        $indikators = Indikator::with('subKategori.kategori.komponen')->where('status', 1)->orderBy('urutan')->get();

        return view('page.pertanyaan.index', compact(
            'pertanyaans',
            'total',
            'aktif',
            'tidakAktif',
            'komponens',
            'kategoris',
            'subKategoris',
            'indikators'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $indikators = Indikator::with('subKategori.kategori.komponen')
            ->where('status', 1)
            ->orderBy('urutan')
            ->get();

        return view('page.pertanyaan.create', compact('indikators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'indikator_id' => 'required|exists:tm_indikator,id',
            'kode' => 'required|max:10',
            'pertanyaan' => 'required',
            'penjelasan' => 'nullable',
            'tipe_jawaban' => 'required|in:ya_tidak,pilihan_ganda,angka,teks',
            'pilihan_jawaban' => 'nullable',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'indikator_id.required' => 'Indikator harus dipilih',
            'indikator_id.exists' => 'Indikator tidak valid',
            'kode.required' => 'Kode harus diisi',
            'kode.max' => 'Kode maksimal 10 karakter',
            'pertanyaan.required' => 'Pertanyaan harus diisi',
            'tipe_jawaban.required' => 'Tipe jawaban harus dipilih',
            'tipe_jawaban.in' => 'Tipe jawaban tidak valid',
            'urutan.required' => 'Urutan harus diisi',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        // Check if kode already exists for this indikator
        $exists = Pertanyaan::where('indikator_id', $request->indikator_id)
            ->where('kode', $request->kode)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['kode' => 'Kode sudah digunakan untuk indikator ini']);
        }

        // Process pilihan_jawaban
        $pilihanJawaban = null;
        if ($request->tipe_jawaban === 'ya_tidak') {
            $pilihanJawaban = ['Ya', 'Tidak'];
        } elseif ($request->tipe_jawaban === 'pilihan_ganda' && $request->filled('pilihan_jawaban')) {
            // Convert comma-separated to array or handle JSON
            if (is_string($request->pilihan_jawaban)) {
                $pilihanJawaban = array_map('trim', explode(',', $request->pilihan_jawaban));
            } else {
                $pilihanJawaban = $request->pilihan_jawaban;
            }
        }

        Pertanyaan::create([
            'indikator_id' => $request->indikator_id,
            'kode' => $request->kode,
            'pertanyaan' => $request->pertanyaan,
            'penjelasan' => $request->penjelasan,
            'tipe_jawaban' => $request->tipe_jawaban,
            'pilihan_jawaban' => $pilihanJawaban,
            'urutan' => $request->urutan,
            'status' => $request->status,
        ]);

        return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pertanyaan $pertanyaan)
    {
        $pertanyaan->load(['indikator.subKategori.kategori.komponen', 'subPertanyaans']);

        return view('page.pertanyaan.show', compact('pertanyaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pertanyaan $pertanyaan)
    {
        $indikators = Indikator::with('subKategori.kategori.komponen')
            ->where('status', 1)
            ->orderBy('urutan')
            ->get();

        return view('page.pertanyaan.edit', compact('pertanyaan', 'indikators'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pertanyaan $pertanyaan)
    {
        $request->validate([
            'indikator_id' => 'required|exists:tm_indikator,id',
            'kode' => 'required|max:10',
            'pertanyaan' => 'required',
            'penjelasan' => 'nullable',
            'tipe_jawaban' => 'required|in:ya_tidak,pilihan_ganda,angka,teks',
            'pilihan_jawaban' => 'nullable',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'indikator_id.required' => 'Indikator harus dipilih',
            'indikator_id.exists' => 'Indikator tidak valid',
            'kode.required' => 'Kode harus diisi',
            'kode.max' => 'Kode maksimal 10 karakter',
            'pertanyaan.required' => 'Pertanyaan harus diisi',
            'tipe_jawaban.required' => 'Tipe jawaban harus dipilih',
            'tipe_jawaban.in' => 'Tipe jawaban tidak valid',
            'urutan.required' => 'Urutan harus diisi',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        // Check if kode already exists for this indikator (exclude current)
        $exists = Pertanyaan::where('indikator_id', $request->indikator_id)
            ->where('kode', $request->kode)
            ->where('id', '!=', $pertanyaan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['kode' => 'Kode sudah digunakan untuk indikator ini']);
        }

        // Process pilihan_jawaban
        $pilihanJawaban = null;
        if ($request->tipe_jawaban === 'ya_tidak') {
            $pilihanJawaban = ['Ya', 'Tidak'];
        } elseif ($request->tipe_jawaban === 'pilihan_ganda' && $request->filled('pilihan_jawaban')) {
            if (is_string($request->pilihan_jawaban)) {
                $pilihanJawaban = array_map('trim', explode(',', $request->pilihan_jawaban));
            } else {
                $pilihanJawaban = $request->pilihan_jawaban;
            }
        }

        $pertanyaan->update([
            'indikator_id' => $request->indikator_id,
            'kode' => $request->kode,
            'pertanyaan' => $request->pertanyaan,
            'penjelasan' => $request->penjelasan,
            'tipe_jawaban' => $request->tipe_jawaban,
            'pilihan_jawaban' => $pilihanJawaban,
            'urutan' => $request->urutan,
            'status' => $request->status,
        ]);

        return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pertanyaan $pertanyaan)
    {
        try {
            $pertanyaan->delete();
            return redirect()->route('pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('pertanyaan.index')->with('error', 'Pertanyaan tidak dapat dihapus karena masih memiliki relasi dengan data lain');
        }
    }
}
