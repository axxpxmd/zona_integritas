<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Periode::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_periode', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter is_template
        if ($request->filled('is_template')) {
            $query->where('is_template', $request->is_template);
        }

        $periodes = $query->latest()->paginate(10)->withQueryString();

        // Stats
        $stats = [
            'total' => Periode::count(),
            'aktif' => Periode::where('status', 'aktif')->count(),
            'draft' => Periode::where('status', 'draft')->count(),
            'template' => Periode::where('is_template', 1)->count(),
        ];

        return view('page.periode.index', compact('periodes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = Periode::template()->get();
        $periodes = Periode::normal()->orderBy('tahun', 'desc')->get();

        return view('page.periode.create', compact('templates', 'periodes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2024|max:2100',
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:draft,aktif,selesai,ditutup',
            'is_template' => 'nullable|boolean',
            'copied_from_periode_id' => 'nullable|exists:tm_periode,id',
        ], [
            'tahun.required' => 'Tahun periode wajib diisi.',
            'tahun.min' => 'Tahun minimal 2024.',
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.max' => 'Nama periode maksimal 100 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'copied_from_periode_id.exists' => 'Periode sumber tidak ditemukan.',
        ]);

        $validated['is_template'] = $request->has('is_template') ? 1 : 0;

        Periode::create($validated);

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Periode $periode)
    {
        $periode->load(['copiedFrom', 'copiedPeriodes']);

        return view('page.periode.show', compact('periode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Periode $periode)
    {
        $templates = Periode::template()->get();
        $periodes = Periode::normal()->where('id', '!=', $periode->id)->orderBy('tahun', 'desc')->get();

        return view('page.periode.edit', compact('periode', 'templates', 'periodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2024|max:2100',
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:draft,aktif,selesai,ditutup',
            'is_template' => 'nullable|boolean',
            'copied_from_periode_id' => 'nullable|exists:tm_periode,id',
        ], [
            'tahun.required' => 'Tahun periode wajib diisi.',
            'tahun.min' => 'Tahun minimal 2024.',
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'nama_periode.max' => 'Nama periode maksimal 100 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'copied_from_periode_id.exists' => 'Periode sumber tidak ditemukan.',
        ]);

        $validated['is_template'] = $request->has('is_template') ? 1 : 0;

        $periode->update($validated);

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periode $periode)
    {
        // Cek apakah ada data terkait
        if ($periode->komponens()->exists() || $periode->jawabans()->exists()) {
            return redirect()->route('periode.index')
                ->with('error', 'Periode tidak dapat dihapus karena memiliki data terkait.');
        }

        $periode->delete();

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil dihapus.');
    }
}
