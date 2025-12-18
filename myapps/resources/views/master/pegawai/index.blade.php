@extends('layouts.dashboard')

@section('title', 'Data Pegawai')
@section('header', 'Data Pegawai')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Cabang aktif:</span>
        <span class="font-medium">{{ session('nama_cabang', 'Tidak ada cabang') }}</span>
    </div>
    <div class="flex items-center gap-2">
        <form action="{{ route('pegawai.index') }}" method="GET" class="flex items-center gap-2" id="searchForm">
            <input 
                type="text" 
                name="search" 
                id="searchInput"
                value="{{ request('search') }}" 
                placeholder="Cari nama, username, email, atau no telp..." 
                class="px-3 py-2 border rounded dark:bg-gray-900 dark:border-gray-700 text-sm"
                autocomplete="off"
            />
        </form>
        <a href="{{ route('pegawai.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Tambah Pegawai</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="text-left py-2 px-3">Nama</th>
                    <th class="text-left py-2 px-3">Username</th>
                    <th class="text-left py-2 px-3">Role</th>
                    <th class="text-left py-2 px-3">Email</th>
                    <th class="text-left py-2 px-3">No Telp</th>
                    <th class="text-center py-2 px-3 w-56">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($currentUserPegawai && !$pegawai->contains('id', $currentUserPegawai->id))
                    @php
                        $p = $currentUserPegawai;
                        $isCurrentUser = true;
                    @endphp
                    <tr class="border-b dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20">
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-2">
                                {{ $p->nama }}
                                <span class="text-xs bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200 px-1.5 py-0.5 rounded">Saya</span>
                            </div>
                        </td>
                        <td class="py-2 px-3">{{ $p->username ?? '-' }}</td>
                        <td class="py-2 px-3">
                            <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 font-bold">
                                {{ ucfirst($p->role) }}
                            </span>
                        </td>
                        <td class="py-2 px-3">{{ $p->email ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $p->no_telp ?? '-' }}</td>
                        <td class="py-2 px-3 text-center">
                            <div class="inline-flex gap-2 justify-center">
                                <a href="{{ route('pegawai.show', $p) }}" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">Show</a>
                                <a href="{{ route('pegawai.edit', $p) }}" class="px-2 py-1 text-xs rounded bg-amber-500 text-white hover:bg-amber-600">Edit</a>
                            </div>
                        </td>
                    </tr>
                @endif
                @forelse($pegawai as $p)
                @php
                    $isCurrentUser = $currentUserPegawai && $currentUserPegawai->id === $p->id;
                @endphp
                <tr class="border-b dark:border-gray-700 {{ $isCurrentUser ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                    <td class="py-2 px-3">
                        <div class="flex items-center gap-2">
                            {{ $p->nama }}
                            @if($isCurrentUser)
                                <span class="text-xs bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200 px-1.5 py-0.5 rounded">Saya</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-2 px-3">{{ $p->username ?? '-' }}</td>
                    <td class="py-2 px-3">
                        <span class="px-2 py-1 rounded text-xs {{ $p->role === 'owner' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : ($p->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }} {{ $isCurrentUser ? 'font-bold' : '' }}">
                            {{ ucfirst($p->role) }}
                        </span>
                    </td>
                    <td class="py-2 px-3">{{ $p->email ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $p->no_telp ?? '-' }}</td>
                    <td class="py-2 px-3 text-center">
                        <div class="inline-flex gap-2 justify-center">
                            <a href="{{ route('pegawai.show', $p) }}" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Show
                            </a>
                            <a href="{{ route('pegawai.edit', $p) }}" class="px-2 py-1 text-xs rounded bg-amber-500 text-white hover:bg-amber-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit
                            </a>
                            @if(!$isCurrentUser)
                            <form action="{{ route('pegawai.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pegawai ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                @if(!$currentUserPegawai)
                <tr>
                    <td colspan="6" class="py-4 px-3 text-center text-gray-500 dark:text-gray-400">Belum ada data</td>
                </tr>
                @endif
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $pegawai->links() }}</div>
</div>

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    
    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            if (this.value.trim() === '') {
                window.location.href = "{{ route('pegawai.index') }}";
                return;
            }
            
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500);
        });
    }
})();
</script>
@endpush
@endsection

