<?php

namespace App\Http\Controllers;

use App\Models\SubKategori;
use App\Models\Kategori;
use App\Models\Komponen;
use Illuminate\Http\Request;

class SubKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubKategori::with(['kategori.komponen']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by komponen
        if ($request->filled('komponen_id')) {
            $query->whereHas('kategori', function($q) use ($request) {
                $q->where('komponen_id', $request->komponen_id);
            });
        }

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subKategoris = $query->orderBy('urutan', 'asc')->paginate(10)->withQueryString();

        // Stats
        $total = SubKategori::count();
        $aktif = SubKategori::where('status', 1)->count();
        $tidakAktif = SubKategori::where('status', 0)->count();

        // Get all komponens and kategoris for filter dropdown
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();

        return view('page.sub-kategori.index', compact('subKategoris', 'total', 'aktif', 'tidakAktif', 'komponens', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();
        return view('page.sub-kategori.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:tm_kategori,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:200',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama sub kategori wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of kategori_id and kode
        $exists = SubKategori::where('kategori_id', $validated['kategori_id'])
                          ->where('kode', $validated['kode'])
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk kategori ini.']);
        }

        SubKategori::create($validated);

        return redirect()->route('sub-kategori.index')
            ->with('success', 'Data sub kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubKategori $subKategori)
    {
        $subKategori->load(['kategori.komponen', 'indikators' => function($q) {
            $q->orderBy('urutan');
        }]);

        return view('page.sub-kategori.show', compact('subKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubKategori $subKategori)
    {
        $kategoris = Kategori::with('komponen')->where('status', 1)->orderBy('urutan')->get();
        return view('page.sub-kategori.edit', compact('subKategori', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubKategori $subKategori)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:tm_kategori,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:200',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama sub kategori wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of kategori_id and kode (excluding current record)
        $exists = SubKategori::where('kategori_id', $validated['kategori_id'])
                          ->where('kode', $validated['kode'])
                          ->where('id', '!=', $subKategori->id)
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk kategori ini.']);
        }

        $subKategori->update($validated);

        return redirect()->route('sub-kategori.index')
            ->with('success', 'Data sub kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubKategori $subKategori)
    {
        try {
            $subKategori->delete();
            return redirect()->route('sub-kategori.index')
                ->with('success', 'Data sub kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('sub-kategori.index')
                ->with('error', 'Data sub kategori tidak dapat dihapus karena masih memiliki relasi dengan data lain.');
        }
    }
}
