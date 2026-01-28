<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Komponen;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kategori::with('komponen');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('komponen', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by komponen
        if ($request->filled('komponen_id')) {
            $query->where('komponen_id', $request->komponen_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kategoris = $query->orderBy('urutan', 'asc')->paginate(10)->withQueryString();

        // Stats
        $total = Kategori::count();
        $aktif = Kategori::where('status', 1)->count();
        $tidakAktif = Kategori::where('status', 0)->count();

        // Get all komponens for filter dropdown
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();

        return view('page.kategori.index', compact('kategoris', 'total', 'aktif', 'tidakAktif', 'komponens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        return view('page.kategori.create', compact('komponens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'komponen_id' => 'required|exists:tm_komponen,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:150',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'komponen_id.required' => 'Komponen wajib dipilih.',
            'komponen_id.exists' => 'Komponen tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama kategori wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of komponen_id and kode
        $exists = Kategori::where('komponen_id', $validated['komponen_id'])
                          ->where('kode', $validated['kode'])
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk komponen ini.']);
        }

        Kategori::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Data kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        $kategori->load(['komponen', 'subKategoris' => function($q) {
            $q->orderBy('urutan');
        }]);

        return view('page.kategori.show', compact('kategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        $komponens = Komponen::where('status', 1)->orderBy('urutan')->get();
        return view('page.kategori.edit', compact('kategori', 'komponens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'komponen_id' => 'required|exists:tm_komponen,id',
            'kode' => 'required|string|max:10',
            'nama' => 'required|string|max:150',
            'bobot' => 'required|numeric|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'urutan' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ], [
            'komponen_id.required' => 'Komponen wajib dipilih.',
            'komponen_id.exists' => 'Komponen tidak valid.',
            'kode.required' => 'Kode wajib diisi.',
            'nama.required' => 'Nama kategori wajib diisi.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.',
            'urutan.required' => 'Urutan wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
        ]);

        // Check unique combination of komponen_id and kode (excluding current record)
        $exists = Kategori::where('komponen_id', $validated['komponen_id'])
                          ->where('kode', $validated['kode'])
                          ->where('id', '!=', $kategori->id)
                          ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['kode' => 'Kode sudah digunakan untuk komponen ini.']);
        }

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Data kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();
            return redirect()->route('kategori.index')
                ->with('success', 'Data kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                ->with('error', 'Data kategori tidak dapat dihapus karena masih memiliki relasi dengan data lain.');
        }
    }
}
