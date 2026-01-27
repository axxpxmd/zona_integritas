<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('opd');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('nama_instansi', 'like', "%{$search}%")
                  ->orWhere('nama_kepala', 'like', "%{$search}%")
                  ->orWhere('nama_operator', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by OPD
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $users = $query->orderBy('nama_instansi', 'asc')->paginate(10)->withQueryString();
        $opds = Opd::where('status', 1)->orderBy('n_opd', 'asc')->get();

        return view('page.user.index', compact('users', 'opds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $opds = Opd::where('status', 1)->orderBy('n_opd', 'asc')->get();
        return view('page.user.create', compact('opds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opd_id' => 'nullable|exists:tm_opd,id',
            'username' => 'required|string|max:255|unique:users,username',
            'nama_instansi' => 'required|string|max:255',
            'nama_kepala' => 'nullable|string|max:255',
            'jabatan_kepala' => 'nullable|string|max:255',
            'nama_operator' => 'nullable|string|max:255',
            'jabatan_operator' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,operator,verifikator',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'nama_instansi.required' => 'Nama instansi wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('user.index')
            ->with('success', 'Data pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('page.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $opds = Opd::where('status', 1)->orderBy('n_opd', 'asc')->get();
        return view('page.user.edit', compact('user', 'opds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'opd_id' => 'nullable|exists:tm_opd,id',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nama_instansi' => 'required|string|max:255',
            'nama_kepala' => 'nullable|string|max:255',
            'jabatan_kepala' => 'nullable|string|max:255',
            'nama_operator' => 'nullable|string|max:255',
            'jabatan_operator' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,operator,verifikator',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'nama_instansi.required' => 'Nama instansi wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('user.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user.index')
            ->with('success', 'Data pengguna berhasil dihapus.');
    }
}
