<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kategori;
use App\Models\Komponen;
use App\Models\Pertanyaan;
use App\Models\SubKategori;
use App\Models\SubPertanyaan;
use Illuminate\Http\Request;

class SubPertanyaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubPertanyaan::with(['pertanyaanUtama.indikator.subKategori.kategori.komponen']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('penjelasan', 'like', "%{$search}%")
                  ->orWhere('formula', 'like', "%{$search}%")
                  ->orWhereHas('pertanyaanUtama', function ($q) use ($search) {
                      $q->where('pertanyaan', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Komponen
        if ($request->filled('komponen_id')) {
            $query->whereHas('pertanyaanUtama.indikator.subKategori.kategori', function ($q) use ($request) {
                $q->where('komponen_id', $request->komponen_id);
            });
        }

        // Filter by Kategori
        if ($request->filled('kategori_id')) {
            $query->whereHas('pertanyaanUtama.indikator.subKategori', function ($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter by Sub Kategori
        if ($request->filled('sub_kategori_id')) {
            $query->whereHas('pertanyaanUtama.indikator', function ($q) use ($request) {
                $q->where('sub_kategori_id', $request->sub_kategori_id);
            });
        }

        // Filter by Indikator
        if ($request->filled('indikator_id')) {
            $query->whereHas('pertanyaanUtama', function ($q) use ($request) {
                $q->where('indikator_id', $request->indikator_id);
            });
        }

        // Filter by Pertanyaan
        if ($request->filled('pertanyaan_id')) {
            $query->where('pertanyaan_id', $request->pertanyaan_id);
        }

        // Filter by Tipe Input
        if ($request->filled('tipe_input')) {
            $query->where('tipe_input', $request->tipe_input);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subPertanyaans = $query->orderBy('urutan')->paginate(10)->withQueryString();

        // Stats
        $total = SubPertanyaan::count();
        $aktif = SubPertanyaan::where('status', 1)->count();
        $tidakAktif = SubPertanyaan::where('status', 0)->count();

        // Dropdowns for filters
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();
        $subKategoris = SubKategori::with('kategori.komponen')->where('status', 1)->orderBy('urutan')->get();
        $indikators = Indikator::with('subKategori.kategori.komponen')->where('status', 1)->orderBy('urutan')->get();
        $pertanyaans = Pertanyaan::with('indikator.subKategori.kategori.komponen')->where('status', 1)->orderBy('urutan')->get();

        return view('page.sub-pertanyaan.index', compact(
            'subPertanyaans',
            'total',
            'aktif',
            'tidakAktif',
            'komponens',
            'kategoris',
            'subKategoris',
            'indikators',
            'pertanyaans'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pertanyaans = Pertanyaan::with('indikator.subKategori.kategori.komponen')
            ->where('status', 1)
            ->orderBy('urutan')
            ->get();

        return view('page.sub-pertanyaan.create', compact('pertanyaans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan_id' => 'required|exists:tm_pertanyaan,id',
            'kode' => 'required|max:20',
            'pertanyaan' => 'required',
            'penjelasan' => 'nullable',
            'tipe_input' => 'required|in:jumlah,persen,teks,angka',
            'satuan' => 'nullable|max:50',
            'formula' => 'nullable',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'pertanyaan_id.required' => 'Pertanyaan utama harus dipilih',
            'pertanyaan_id.exists' => 'Pertanyaan utama tidak valid',
            'kode.required' => 'Kode harus diisi',
            'kode.max' => 'Kode maksimal 20 karakter',
            'pertanyaan.required' => 'Sub pertanyaan harus diisi',
            'tipe_input.required' => 'Tipe input harus dipilih',
            'tipe_input.in' => 'Tipe input tidak valid',
            'satuan.max' => 'Satuan maksimal 50 karakter',
            'urutan.required' => 'Urutan harus diisi',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        // Check if kode already exists for this pertanyaan
        $exists = SubPertanyaan::where('pertanyaan_id', $request->pertanyaan_id)
            ->where('kode', $request->kode)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['kode' => 'Kode sudah digunakan untuk pertanyaan ini']);
        }

        SubPertanyaan::create($request->all());

        return redirect()->route('sub-pertanyaan.index')->with('success', 'Sub Pertanyaan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubPertanyaan $subPertanyaan)
    {
        $subPertanyaan->load(['pertanyaanUtama.indikator.subKategori.kategori.komponen']);

        return view('page.sub-pertanyaan.show', compact('subPertanyaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubPertanyaan $subPertanyaan)
    {
        $pertanyaans = Pertanyaan::with('indikator.subKategori.kategori.komponen')
            ->where('status', 1)
            ->orderBy('urutan')
            ->get();

        return view('page.sub-pertanyaan.edit', compact('subPertanyaan', 'pertanyaans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubPertanyaan $subPertanyaan)
    {
        $request->validate([
            'pertanyaan_id' => 'required|exists:tm_pertanyaan,id',
            'kode' => 'required|max:20',
            'pertanyaan' => 'required',
            'penjelasan' => 'nullable',
            'tipe_input' => 'required|in:jumlah,persen,teks,angka',
            'satuan' => 'nullable|max:50',
            'formula' => 'nullable',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'pertanyaan_id.required' => 'Pertanyaan utama harus dipilih',
            'pertanyaan_id.exists' => 'Pertanyaan utama tidak valid',
            'kode.required' => 'Kode harus diisi',
            'kode.max' => 'Kode maksimal 20 karakter',
            'pertanyaan.required' => 'Sub pertanyaan harus diisi',
            'tipe_input.required' => 'Tipe input harus dipilih',
            'tipe_input.in' => 'Tipe input tidak valid',
            'satuan.max' => 'Satuan maksimal 50 karakter',
            'urutan.required' => 'Urutan harus diisi',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 0',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        // Check if kode already exists for this pertanyaan (exclude current)
        $exists = SubPertanyaan::where('pertanyaan_id', $request->pertanyaan_id)
            ->where('kode', $request->kode)
            ->where('id', '!=', $subPertanyaan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['kode' => 'Kode sudah digunakan untuk pertanyaan ini']);
        }

        $subPertanyaan->update($request->all());

        return redirect()->route('sub-pertanyaan.index')->with('success', 'Sub Pertanyaan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubPertanyaan $subPertanyaan)
    {
        try {
            $subPertanyaan->delete();
            return redirect()->route('sub-pertanyaan.index')->with('success', 'Sub Pertanyaan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('sub-pertanyaan.index')->with('error', 'Sub Pertanyaan tidak dapat dihapus karena masih memiliki relasi dengan data lain');
        }
    }
}
