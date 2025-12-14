<?php

namespace App\Helpers;

use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;

class BranchHelper
{
    /**
     * Get current branch ID from session
     */
    public static function getCurrentBranchId()
    {
        return session('id_cabang');
    }

    /**
     * Get current branch name from session
     */
    public static function getCurrentBranchName()
    {
        return session('nama_cabang', 'Tidak ada cabang');
    }

    /**
     * Check if branch is set in session
     */
    public static function hasBranch()
    {
        return session()->has('id_cabang');
    }

    /**
     * Apply branch filter to query builder
     */
    public static function applyBranchFilter($query, $branchColumn = 'id_cabang')
    {
        if (self::hasBranch()) {
            return $query->where($branchColumn, self::getCurrentBranchId());
        }
        return $query;
    }

    /**
     * Check if current user can access a branch
     */
    public static function canAccessBranch($idCabang)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Get pegawai from user email (jika ada)
        $userEmail = $user->email ?? null;
        $pegawai = null;
        
        if ($userEmail) {
            $pegawai = Pegawai::where('email', $userEmail)->first();
        }
        
        if (!$pegawai) {
            return false;
        }

        return $pegawai->canAccessCabang($idCabang);
    }

    /**
     * Get accessible branches for current user
     */
    public static function getAccessibleBranches()
    {
        $user = Auth::user();
        if (!$user) {
            return collect([]);
        }

        // Get pegawai from user email (jika ada)
        $userEmail = $user->email ?? null;
        $pegawai = null;
        
        if ($userEmail) {
            $pegawai = Pegawai::where('email', $userEmail)->first();
        }
        
        if (!$pegawai) {
            return collect([]);
        }

        return $pegawai->getAccessibleCabangs();
    }

    /**
     * Get current user role
     */
    public static function getCurrentUserRole()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Cari pegawai berdasarkan username atau email
        $pegawai = Pegawai::where('username', $user->username)
            ->orWhere(function($q) use ($user) {
                if ($user->email) {
                    $q->where('email', $user->email);
                }
            })
            ->first();
        
        return $pegawai ? $pegawai->role : null;
    }

    /**
     * Check if current user is owner
     */
    public static function isOwner()
    {
        $role = self::getCurrentUserRole();
        // Support backward compatibility dengan "super admin"
        return $role === 'owner' || $role === 'super admin';
    }

    /**
     * Check if current user is admin
     */
    public static function isAdmin()
    {
        $role = self::getCurrentUserRole();
        return $role === 'admin';
    }

    /**
     * Check if current user is pegawai
     */
    public static function isPegawai()
    {
        $role = self::getCurrentUserRole();
        return $role === 'pegawai';
    }
}

