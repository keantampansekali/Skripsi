<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $idCabang = (int) session('id_cabang');
        $suppliers = Supplier::where('id_cabang', $idCabang)
            ->withCount('contacts')
            ->latest()
            ->paginate(10);
        return view('master.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('master.supplier.create');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('contacts');
        return view('master.supplier.show', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kontak' => ['array'],
            'kontak.*.tipe' => ['required_with:kontak', 'string', 'max:20'],
            'kontak.*.nilai' => ['required_with:kontak', 'string', 'max:255'],
        ]);

        $validated['id_cabang'] = (int) session('id_cabang');

        DB::transaction(function () use ($validated) {
            $supplier = Supplier::create([
                'id_cabang' => $validated['id_cabang'],
                'nama_supplier' => $validated['nama_supplier'],
                'alamat' => $validated['alamat'] ?? null,
            ]);

            foreach (($validated['kontak'] ?? []) as $row) {
                if (!empty($row['nilai'])) {
                    $supplier->contacts()->create([
                        'tipe' => $row['tipe'] ?? 'telp',
                        'nilai' => $row['nilai'],
                    ]);
                }
            }
        });

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dibuat');
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('contacts');
        return view('master.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kontak' => ['array'],
            'kontak.*.tipe' => ['required_with:kontak', 'string', 'max:20'],
            'kontak.*.nilai' => ['required_with:kontak', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($supplier, $validated) {
            $supplier->update([
                'nama_supplier' => $validated['nama_supplier'],
                'alamat' => $validated['alamat'] ?? null,
            ]);
            $supplier->contacts()->delete();
            foreach (($validated['kontak'] ?? []) as $row) {
                if (!empty($row['nilai'])) {
                    $supplier->contacts()->create([
                        'tipe' => $row['tipe'] ?? 'telp',
                        'nilai' => $row['nilai'],
                    ]);
                }
            }
        });

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus');
    }
}


