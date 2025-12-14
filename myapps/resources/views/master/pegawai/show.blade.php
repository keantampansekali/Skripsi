@extends('layouts.dashboard')

@section('title', 'Detail Pegawai')
@section('header', 'Detail Pegawai')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        
        <div>
            <label class="block text-sm mb-1">Nama</label>
            <input value="{{ $pegawai->nama }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div>
            <label class="block text-sm mb-1">Username</label>
            <input value="{{ $pegawai->username ?? '-' }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div>
            <label class="block text-sm mb-1">Role</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700">
                <span class="px-2 py-1 rounded text-xs {{ $pegawai->role === 'owner' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : ($pegawai->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                    {{ ucfirst($pegawai->role) }}
                </span>
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input value="{{ $pegawai->email ?? '-' }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div>
            <label class="block text-sm mb-1">No Telp</label>
            <input value="{{ $pegawai->no_telp ?? '-' }}" readonly class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700 cursor-not-allowed" />
        </div>

        <div>
            <label class="block text-sm mb-1">Akses Cabang</label>
            <div class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-700">
                @if($pegawai->role === 'owner')
                    <span class="text-sm font-medium text-purple-600 dark:text-purple-400">Semua Cabang (Owner)</span>
                @else
                    @if($pegawai->cabangs->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($pegawai->cabangs as $cabang)
                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $cabang->nama_cabang }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-sm text-gray-500">Belum ada cabang yang di-assign</span>
                    @endif
                @endif
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('pegawai.index') }}" class="px-3 py-2 rounded border dark:border-gray-700">Kembali</a>
            <a href="{{ route('pegawai.edit', $pegawai) }}" class="px-3 py-2 rounded bg-amber-500 text-white">Edit</a>
        </div>
    </div>
</div>
@endsection

