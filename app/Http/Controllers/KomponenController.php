<?php

namespace App\Http\Controllers;

use App\Models\Komponen;
use Illuminate\Http\Request;

class KomponenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Komponen::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $komponens = $query->orderBy('urutan', 'asc')->paginate(10)->withQueryString();

        // Stats
        $total = Komponen::count();
        $aktif = Komponen::where('status', 1)->count();
        $tidakAktif = Komponen::where('status', 0)->count();

        return view('page.komponen.index', compact('komponens', 'total', 'aktif', 'tidakAktif'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('page.komponen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:tm_komponen,kode',
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama komponen wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        Komponen::create($validated);

        return redirect()->route('komponen.index')
            ->with('success', 'Data komponen berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Komponen $komponen)
    {
        $komponen->load(['kategoris' => function($q) {
            $q->orderBy('urutan');
        }]);

        return view('page.komponen.show', compact('komponen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Komponen $komponen)
    {
        return view('page.komponen.edit', compact('komponen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Komponen $komponen)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:tm_komponen,kode,' . $komponen->id,
            'nama' => 'required|string|max:100',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama komponen wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        $komponen->update($validated);

        return redirect()->route('komponen.index')
            ->with('success', 'Data komponen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komponen $komponen)
    {
        try {
            $komponen->delete();
            return redirect()->route('komponen.index')
                ->with('success', 'Data komponen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('komponen.index')
                ->with('error', 'Data komponen tidak dapat dihapus karena masih memiliki relasi dengan data lain.');
        }
    }
}
