@extends('layouts.dashboard')

@section('title', 'Detail Supplier')
@section('header', 'Detail Supplier')

@section('content')
<div class="max-w-3xl space-y-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-gray-500 dark:text-gray-400">Nama</div>
                <div class="font-medium">{{ $supplier->nama_supplier }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-gray-500 dark:text-gray-400">Alamat</div>
                <div class="font-medium">{{ $supplier->alamat ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="mb-2 font-semibold">Kontak</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="text-left py-2 px-3">Tipe</th>
                        <th class="text-left py-2 px-3">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->contacts as $c)
                    <tr class="border-b dark:border-gray-700">
                        <td class="py-2 px-3">{{ strtoupper($c->tipe) }}</td>
                        <td class="py-2 px-3">{{ $c->nilai }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-3 px-3 text-gray-500 dark:text-gray-400">Tidak ada kontak</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('supplier.index') }}" class="px-3 py-2 rounded border dark:border-gray-700">Kembali</a>
        <a href="{{ route('supplier.edit', $supplier) }}" class="px-3 py-2 rounded bg-amber-500 text-white">Edit</a>
    </div>
</div>
@endsection


