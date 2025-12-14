@extends('layouts.dashboard')

@section('title', 'Edit Pegawai')
@section('header', 'Edit Pegawai')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('pegawai.update', $pegawai) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">Nama</label>
            <input name="nama" value="{{ old('nama', $pegawai->nama) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            @error('nama')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Username</label>
            <input name="username" value="{{ old('username', $pegawai->username) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required />
            <p class="text-xs text-gray-500 mt-1">Untuk login</p>
            @error('username')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Role</label>
            <select name="role" id="role" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" required onchange="toggleCabangsSection()">
                <option value="">Pilih Role</option>
                <option value="pegawai" {{ old('role', $pegawai->role) === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                <option value="admin" {{ old('role', $pegawai->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="owner" {{ old('role', $pegawai->role) === 'owner' ? 'selected' : '' }}>Owner</option>
            </select>
            @error('role')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            <p class="text-xs text-gray-500 mt-1">Opsional</p>
            @error('email')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" minlength="6" />
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.</p>
            @error('password')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">No Telp</label>
            <input type="text" name="no_telp" value="{{ old('no_telp', $pegawai->no_telp) }}" class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700" />
            @error('no_telp')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div id="cabangs-section">
            <label class="block text-sm mb-1">Akses Cabang</label>
            <p class="text-xs text-gray-500 mb-2">Pilih cabang yang bisa diakses (kosongkan jika Owner - bisa akses semua cabang)</p>
            <div class="space-y-2 max-h-48 overflow-y-auto border rounded p-2 dark:bg-gray-900 dark:border-gray-700">
                @foreach($cabangs as $cabang)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="cabangs[]" value="{{ $cabang->id_cabang }}" 
                        {{ in_array($cabang->id_cabang, old('cabangs', $pegawai->cabangs->pluck('id_cabang')->toArray())) ? 'checked' : '' }} 
                        class="rounded" />
                    <span>{{ $cabang->nama_cabang }}</span>
                </label>
                @endforeach
            </div>
            @error('cabangs')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="flex gap-2">
            <a href="{{ route('pegawai.index') }}" class="px-3 py-2 rounded border dark:border-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Batal
            </a>
            <button class="px-3 py-2 rounded bg-blue-600 text-white flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Update
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleCabangsSection() {
    const role = document.getElementById('role').value;
    const cabangsSection = document.getElementById('cabangs-section');
    
    if (role === 'owner') {
        cabangsSection.style.display = 'none';
        // Uncheck all cabangs
        document.querySelectorAll('input[name="cabangs[]"]').forEach(cb => cb.checked = false);
    } else {
        cabangsSection.style.display = 'block';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleCabangsSection();
});
</script>
@endpush
@endsection

