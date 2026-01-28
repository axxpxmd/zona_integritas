<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\SubKategori;
use App\Models\Kategori;
use App\Models\Komponen;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Indikator::with(['subKategori.kategori.komponen']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('subKategori', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by komponen
        if ($request->filled('komponen_id')) {
            $query->whereHas('subKategori.kategori', function($q) use ($request) {
                $q->where('komponen_id', $request->komponen_id);
            });
        }

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->whereHas('subKategori', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter by sub kategori
        if ($request->filled('sub_kategori_id')) {
            $query->where('sub_kategori_id', $request->sub_kategori_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $indikators = $query->orderBy('urutan', 'asc')->paginate(10)->withQueryString();

        // Stats
        $total = Indikator::count();
        $aktif = Indikator::where('status', 1)->count();
        $tidakAktif = Indikator::where('status', 0)->count();

        // Get all komponens, kategoris, and sub kategoris for filter dropdown
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();
        $subKategoris = SubKategori::with('kategori.komponen')->where('status', 1)->orderBy('urutan')->get();

        return view('page.indikator.index', compact('indikators', 'total', 'aktif', 'tidakAktif', 'komponens', 'kategoris', 'subKategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subKategoris = SubKategori::with('kategori.komponen')->where('status', 1)->orderBy('urutan')->get();
        return view('page.indikator.create', compact('subKategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_kategori_id' => 'required|exists:tm_sub_kategori,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:250',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'sub_kategori_id.required' => 'Sub Kategori wajib dipilih.',
            'sub_kategori_id.exists' => 'Sub Kategori tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama indikator wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of sub_kategori_id and kode
        $exists = Indikator::where('sub_kategori_id', $validated['sub_kategori_id'])
                          ->where('kode', $validated['kode'])
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk sub kategori ini.']);
        }

        Indikator::create($validated);

        return redirect()->route('indikator.index')
            ->with('success', 'Data indikator berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Indikator $indikator)
    {
        $indikator->load(['subKategori.kategori.komponen', 'pertanyaans' => function($q) {
            $q->orderBy('urutan');
        }]);

        return view('page.indikator.show', compact('indikator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indikator $indikator)
    {
        $subKategoris = SubKategori::with('kategori.komponen')->where('status', 1)->orderBy('urutan')->get();
        return view('page.indikator.edit', compact('indikator', 'subKategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Indikator $indikator)
    {
        $validated = $request->validate([
            'sub_kategori_id' => 'required|exists:tm_sub_kategori,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:250',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'sub_kategori_id.required' => 'Sub Kategori wajib dipilih.',
            'sub_kategori_id.exists' => 'Sub Kategori tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama indikator wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of sub_kategori_id and kode (excluding current record)
        $exists = Indikator::where('sub_kategori_id', $validated['sub_kategori_id'])
                          ->where('kode', $validated['kode'])
                          ->where('id', '!=', $indikator->id)
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk sub kategori ini.']);
        }

        $indikator->update($validated);

        return redirect()->route('indikator.index')
            ->with('success', 'Data indikator berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indikator $indikator)
    {
        try {
            $indikator->delete();
            return redirect()->route('indikator.index')
                ->with('success', 'Data indikator berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('indikator.index')
                ->with('error', 'Data indikator tidak dapat dihapus karena masih memiliki relasi dengan data lain.');
        }
    }
}
