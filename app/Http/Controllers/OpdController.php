<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Opd::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('n_opd', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opds = $query->orderBy('n_opd', 'asc')->paginate(10)->withQueryString();

        return view('page.opd.index', compact('opds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('page.opd.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'n_opd' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'status' => 'required|in:0,1',
        ], [
            'n_opd.required' => 'Nama OPD wajib diisi.',
            'n_opd.max' => 'Nama OPD maksimal 255 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        Opd::create($validated);

        return redirect()->route('opd.index')
            ->with('success', 'Data OPD berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opd $opd)
    {
        return view('page.opd.edit', compact('opd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Opd $opd)
    {
        $validated = $request->validate([
            'n_opd' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'status' => 'required|in:0,1',
        ], [
            'n_opd.required' => 'Nama OPD wajib diisi.',
            'n_opd.max' => 'Nama OPD maksimal 255 karakter.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $opd->update($validated);

        return redirect()->route('opd.index')
            ->with('success', 'Data OPD berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opd $opd)
    {
        $opd->delete();

        return redirect()->route('opd.index')
            ->with('success', 'Data OPD berhasil dihapus.');
    }
}
