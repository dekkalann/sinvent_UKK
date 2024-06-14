<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Barang;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Buat query untuk pencarian
        $query = BarangMasuk::with('barang');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tgl_masuk', 'like', '%' . $search . '%')
                ->orWhere('barang_id', 'like', '%' . $search . '%')
                ->orWhereHas('barang', function($q) use ($search) {
                    $q->where('merk', 'like', '%' . $search . '%')
                        ->orWhere('seri', 'like', '%' . $search . '%')
                        ->orWhere('spesifikasi', 'like', '%' . $search . '%');
                });
            });
        }

        $rsetBarangMasuk = $query->paginate(100);

        return view('v_barangmasuk.index', compact('rsetBarangMasuk', 'search'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aBarang = Barang::select('id', \DB::raw("CONCAT(merk, ' - ', seri) AS merkseri"))
        ->pluck('merkseri', 'id');        
        $aBarang->prepend('Pilih Barang', ''); // Tambahkan opsi default
        
        return view('v_barangmasuk.create', compact('aBarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validasi input sesuai kebutuhan Anda
         $request->validate([
            'tgl_masuk' => 'required',
            'qty_masuk' => 'required|integer|min:1',
            'barang_id' => 'required',
        ]);

        // Proses menyimpan data barang ke tabel 'barang'
        BarangMasuk::create($request->all());

        return redirect()->route('barangmasuk.index')->with('success', 'Data Barang Masuk berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarangMasuk = BarangMasuk::find($id);

        return view('v_barangmasuk.show', compact('rsetBarangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangmasuk = BarangMasuk::findOrFail($id);
        $aBarang = Barang::select('id', \DB::raw("CONCAT(merk, ' - ', seri) AS merkseri"))
        ->pluck('merkseri', 'id');

        return view('v_barangmasuk.edit', compact('barangmasuk', 'aBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_masuk' => 'required',
            'qty_masuk' => 'required|integer|min:1',
            // 'barang_id' => 'required|exists:barang,id',
        ]);

        $barangmasuk = BarangMasuk::findOrFail($id);
        
        // Hitung stok barang setelah update
        $barang = Barang::find($barangmasuk->barang_id);
        $stok_awal = $barang->stok;
        $stok_baru = $stok_awal - $barangmasuk->qty_masuk + $request->qty_masuk;

        if ($stok_baru < 0) {
            return redirect()->route('barangmasuk.index')->with('error', 'Barang tidak dapat diedit karena stok akan menjadi kurang dari 0.');
        }

        $barangmasuk->update($request->all());

        return redirect()->route('barangmasuk.index')->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Dapatkan data barang masuk yang akan dihapus
        $barangMasuk = BarangMasuk::find($id);

        // Periksa apakah barang sedang digunakan di tabel barang keluar
        // $barangKeluarCount = BarangKeluar::where('barang_id', $barangMasuk->barang_id)->count();

        // // Jika ada barang keluar yang menggunakan barang masuk ini, kembalikan pesan error
        // if ($barangKeluarCount > 0) {
        //     return redirect()->route('barangmasuk.index')->with('error', 'Barang sedang digunakan di barang keluar.');
        // }

        // Periksa stok barang setelah penghapusan
        $barang = Barang::find($barangMasuk->barang_id);
        $stok_baru = $barang->stok - $barangMasuk->qty_masuk;

        if ($stok_baru < 0) {
            return redirect()->route('barangmasuk.index')->with('error', 'Barang tidak dapat dihapus. Stok tidak mencukupi.');
        }

        // Jika tidak ada barang keluar yang menggunakan barang masuk ini, lanjutkan dengan penghapusan
        $barangMasuk->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('barangmasuk.index')->with('success', 'Data Barang Masuk berhasil dihapus.');
    }
}
