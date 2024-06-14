<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $rsetBarang = Barang::orderBy('id', 'asc')->paginate(10);
    //     return view('v_barang.index', compact('rsetBarang'));
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Buat query untuk pencarian
        $query = Barang::with('kategori');

        if ($search) {
            $query->where('merk', 'like', '%' . $search . '%')
                ->orWhere('spesifikasi', 'like', '%' . $search . '%')
                ->orWhereHas('kategori', function($q) use ($search) {
                    $q->where('kategori', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(CASE
                            WHEN kategori = "M" THEN "Modal"
                            WHEN kategori = "A" THEN "Alat"
                            WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
                            ELSE "Bahan Tidak Habis Pakai"
                            END)'), 'like', '%' . $search . '%');
                });
        }

        // Dapatkan hasil query
        $rsetBarang = $query->orderBy('id', 'asc')->paginate(10);

        return view('v_barang.index', compact('rsetBarang', 'search'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aKategori = Kategori::pluck('deskripsi', 'id'); // Ganti 'nama_kolom_kategori' dengan nama kolom kategori yang sesuai di tabel
        $aKategori->prepend('Pilih Kategori', ''); // Tambahkan opsi default
        
        return view('v_barang.create', compact('aKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'kategori_id' => 'required',
        ]);

        // Check if a barang with the same merk and seri already exists
        $existingBarang = Barang::where('merk', $request->merk)
                                ->where('seri', $request->seri)
                                ->first();

        if ($existingBarang) {
            return redirect()->back()->with('error', 'Barang dengan merk dan seri yang sama sudah ada.');
        }

        Barang::create($request->all());
        return redirect()->route('barang.index')->with('success', 'Data Barang Berhasil Disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarang = Barang::find($id);
        return view('v_barang.show', compact('rsetBarang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        $aKategori = Kategori::pluck('deskripsi', 'id');

        return view('v_barang.edit', compact('barang', 'aKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'merk' => 'required',
            'seri' => 'required',
            'spesifikasi' => 'required',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        $barang = Barang::findOrFail($id);

        // Check if a barang with the same merk and seri already exists (excluding the current barang)
        $existingBarang = Barang::where('merk', $request->merk)
                                ->where('seri', $request->seri)
                                ->where('id', '<>', $id)
                                ->first();

        if ($existingBarang) {
            return redirect()->back()->with('error', 'Barang dengan merk dan seri yang sama sudah ada.');
        }

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);

        // Check if the barang is used in barang_masuk or barang_keluar
        // $isUsedInBarangMasuk = BarangMasuk::where('barang_id', $id)->exists();
        // $isUsedInBarangKeluar = BarangKeluar::where('barang_id', $id)->exists();

        // if ($isUsedInBarangMasuk || $isUsedInBarangKeluar) {
        //     return redirect()->route('barang.index')->with('error', 'Barang tidak dapat dihapus karena sedang digunakan di barang masuk atau keluar.');
        // }

        // Delete the barang
        $barang->delete();

        // Redirect to index with success message
        return redirect()->route('barang.index')->with('success', 'Data Barang Berhasil Dihapus!');
    }
}
