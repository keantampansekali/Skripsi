<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\BranchHelper;

class PegawaiController extends Controller
{
    /**
     * Check if user is owner, abort if not
     */
    private function checkOwner()
    {
        if (!BranchHelper::isOwner()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini. Hanya Owner yang dapat mengakses Data Pegawai.');
        }
    }

    public function index(Request $request)
    {
        $this->checkOwner();
        
        $query = Pegawai::query();
        $currentUser = Auth::user();

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        $pegawai = $query->latest()->paginate(10)->withQueryString();
        
        // Cek apakah user yang sedang login sudah ada di tabel pegawai
        $currentUserPegawai = null;
        if ($currentUser) {
            $userEmail = $currentUser->email ?? null;
            
            // Cari berdasarkan email jika ada, atau null jika tidak ada
            if ($userEmail) {
                $currentUserPegawai = Pegawai::where('email', $userEmail)->first();
            }
            
            // Jika belum ada, buat otomatis sebagai owner
            if (!$currentUserPegawai) {
                // Gunakan DB::table untuk bypass mutator karena password sudah di-hash
                $pegawaiId = DB::table('pegawai')->insertGetId([
                    'nama' => $currentUser->name ?? $currentUser->username,
                    'username' => $currentUser->username, // Tambahkan username dari user
                    'email' => $userEmail, // Bisa null
                    'role' => 'owner',
                    'password' => $currentUser->password, // Password sudah di-hash
                    'no_telp' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $currentUserPegawai = Pegawai::find($pegawaiId);
            } else {
                // Update role menjadi owner jika belum
                if ($currentUserPegawai->role !== 'owner') {
                    DB::table('pegawai')->where('id', $currentUserPegawai->id)->update(['role' => 'owner']);
                    $currentUserPegawai->refresh();
                }
            }
        }
        
        return view('master.pegawai.index', compact('pegawai', 'currentUserPegawai', 'currentUser'));
    }

    public function create()
    {
        $this->checkOwner();
        
        $cabangs = Cabang::all();
        return view('master.pegawai.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $this->checkOwner();
        
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:pegawai,username'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'cabangs' => ['nullable', 'array'],
            'cabangs.*' => ['integer', 'exists:tabel_cabang,id_cabang'],
        ]);

        // Validasi unique email jika diisi
        if (!empty($validated['email'])) {
            $request->validate([
                'email' => ['unique:pegawai,email'],
            ]);
        }

        DB::transaction(function () use ($validated) {
            $pegawai = Pegawai::create($validated);
            
            // Assign cabang jika bukan owner
            if ($validated['role'] !== 'owner' && isset($validated['cabangs'])) {
                $pegawai->cabangs()->sync($validated['cabangs']);
            }
        });

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dibuat');
    }

    public function show(Pegawai $pegawai)
    {
        $this->checkOwner();
        
        $pegawai->load('cabangs');
        return view('master.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $this->checkOwner();
        
        $pegawai->load('cabangs');
        $cabangs = Cabang::all();
        return view('master.pegawai.edit', compact('pegawai', 'cabangs'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->checkOwner();
        
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:pegawai,username,' . $pegawai->id],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'cabangs' => ['nullable', 'array'],
            'cabangs.*' => ['integer', 'exists:tabel_cabang,id_cabang'],
        ]);

        // Validasi unique email jika diisi
        if (!empty($validated['email'])) {
            $request->validate([
                'email' => ['unique:pegawai,email,' . $pegawai->id],
            ]);
        }

        // Jika password kosong, hapus dari validated agar tidak diupdate
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        DB::transaction(function () use ($pegawai, $validated) {
            $cabangs = $validated['cabangs'] ?? [];
            unset($validated['cabangs']);
            
            $pegawai->update($validated);
            
            // Sync cabang: owner tidak perlu assign cabang, admin/pegawai perlu
            if ($validated['role'] === 'owner') {
                $pegawai->cabangs()->detach(); // Hapus semua cabang untuk owner
            } else {
                $pegawai->cabangs()->sync($cabangs);
            }
        });

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui');
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->checkOwner();
        
        $pegawai->delete();
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus');
    }
}

