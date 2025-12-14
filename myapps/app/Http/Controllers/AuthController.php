<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cabang;
use App\Models\Pegawai;
use App\Helpers\BranchHelper;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        // Get accessible branches for current user (if any username is pre-filled)
        $accessibleCabangs = collect([]);
        if (old('username')) {
            // Cek dulu di tabel pegawai
            $pegawai = Pegawai::where('username', old('username'))->first();
            
            if ($pegawai) {
                $accessibleCabangs = $pegawai->getAccessibleCabangs();
            } else {
                // Fallback: cek di tabel users (backward compatibility)
                $user = \App\Models\User::where('username', old('username'))->first();
                if ($user) {
                    $userEmail = $user->email ?? null;
                    
                    // Cari pegawai berdasarkan email jika ada
                    if ($userEmail) {
                        $pegawai = Pegawai::where('email', $userEmail)->first();
                    }
                    
                    if ($pegawai) {
                        $accessibleCabangs = $pegawai->getAccessibleCabangs();
                    }
                }
            }
        }

        return view('auth.login', compact('accessibleCabangs'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
            'id_cabang' => ['required','integer','exists:tabel_cabang,id_cabang'],
        ]);

        // Cari pegawai berdasarkan username
        $pegawai = Pegawai::where('username', $credentials['username'])->first();

        // Jika pegawai tidak ditemukan, coba cari di tabel users (untuk backward compatibility)
        if (!$pegawai) {
            // Coba login dengan tabel users (backward compatibility)
            if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
                $request->session()->regenerate();
                
                // Get user dan pegawai
                $user = Auth::user();
                $userEmail = $user->email ?? null;
                $pegawai = null;
                
                // Cari pegawai berdasarkan email atau username
                if ($userEmail) {
                    $pegawai = Pegawai::where('email', $userEmail)->first();
                }
                
                // Jika tidak ditemukan berdasarkan email, coba berdasarkan username
                if (!$pegawai) {
                    $pegawai = Pegawai::where('username', $user->username)->first();
                }
                
                // Jika masih tidak ada, buat otomatis sebagai owner
                if (!$pegawai) {
                    // Gunakan DB::table untuk bypass mutator karena password sudah di-hash
                    $pegawaiId = \Illuminate\Support\Facades\DB::table('pegawai')->insertGetId([
                        'nama' => $user->name ?? $user->username,
                        'username' => $user->username,
                        'email' => $userEmail,
                        'role' => 'owner',
                        'password' => $user->password, // Password sudah di-hash
                        'no_telp' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $pegawai = Pegawai::find($pegawaiId);
                } else {
                    // Update role menjadi owner jika masih "super admin" (backward compatibility)
                    if ($pegawai->role === 'super admin') {
                        $pegawai->update(['role' => 'owner']);
                    }
                }
                
                // Check apakah pegawai bisa akses cabang yang dipilih
                if (!$pegawai->canAccessCabang($credentials['id_cabang'])) {
                    Auth::logout();
                    return back()->withErrors(['id_cabang' => 'Anda tidak memiliki akses ke cabang ini.'])->onlyInput('username');
                }
                
                // store selected cabang ID and name in session for later use
                $cabang = Cabang::find($credentials['id_cabang']);
                $request->session()->put('id_cabang', $cabang->id_cabang);
                $request->session()->put('nama_cabang', $cabang->nama_cabang);
                return redirect()->intended('/dashboard');
            }
        } else {
            // Login menggunakan tabel pegawai
            // Verify password
            if (\Illuminate\Support\Facades\Hash::check($credentials['password'], $pegawai->password)) {
                // Check apakah pegawai bisa akses cabang yang dipilih
                if (!$pegawai->canAccessCabang($credentials['id_cabang'])) {
                    return back()->withErrors(['id_cabang' => 'Anda tidak memiliki akses ke cabang ini.'])->onlyInput('username');
                }

                // Buat atau update user di tabel users untuk session (jika belum ada)
                $user = \App\Models\User::where('username', $pegawai->username)->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => $pegawai->nama,
                        'username' => $pegawai->username,
                        'email' => $pegawai->email ?? $pegawai->username . '@example.com',
                        'password' => $pegawai->password, // Password sudah di-hash
                    ]);
                } else {
                    // Update user jika ada perubahan
                    $user->update([
                        'name' => $pegawai->nama,
                        'email' => $pegawai->email ?? $pegawai->username . '@example.com',
                        'password' => $pegawai->password, // Update password jika berubah
                    ]);
                }

                // Login dengan user untuk session management
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                
                // Store selected cabang ID and name in session
                $cabang = Cabang::find($credentials['id_cabang']);
                $request->session()->put('id_cabang', $cabang->id_cabang);
                $request->session()->put('nama_cabang', $cabang->nama_cabang);
                $request->session()->put('pegawai_id', $pegawai->id); // Store pegawai ID for reference
                
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // Logout user first
        Auth::logout();
        
        // Invalidate session (this will clear all session data)
        $request->session()->invalidate();
        
        // Regenerate CSRF token for new session
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('message', 'Anda telah berhasil logout.');
    }
}


