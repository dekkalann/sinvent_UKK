<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Barang;
use App\Models\BarangMasuk;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $rsetBarangKeluar = BarangKeluar::orderBy('id', 'asc')->paginate(1000);
    //     return view('v_barangkeluar.index', compact('rsetBarangKeluar'));
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Buat query untuk pencarian
        $query = BarangKeluar::with('barang');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tgl_keluar', 'like', '%' . $search . '%')
                ->orWhere('barang_id', 'like', '%' . $search . '%')
                ->orWhereHas('barang', function($q) use ($search) {
                    $q->where('merk', 'like', '%' . $search . '%')
                        ->orWhere('seri', 'like', '%' . $search . '%')
                        ->orWhere('spesifikasi', 'like', '%' . $search . '%');
                });
            });
        }

        $rsetBarangKeluar = $query->paginate(100);

        return view('v_barangkeluar.index', compact('rsetBarangKeluar', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aBarang = Barang::select('id', \DB::raw("CONCAT(merk, ' - ', seri) AS merkseri"))
        ->pluck('merkseri', 'id');        
        $aBarang->prepend('Pilih Barang', ''); // Tambahkan opsi default
        
        return view('v_barangkeluar.create', compact('aBarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input sesuai kebutuhan Anda
        $request->validate([
            'tgl_keluar' => 'required|date',
            'qty_keluar' => 'required|integer|min:1',
            'barang_id' => 'required',
        ]);

        // Dapatkan data barang berdasarkan ID
        $barang = Barang::findOrFail($request->barang_id);

        // Periksa apakah tanggal keluar lebih kecil dari tanggal masuk
        $barangMasukTerawal = BarangMasuk::where('barang_id', $request->barang_id)->orderBy('tgl_masuk', 'asc')->first();
        if ($barangMasukTerawal && $request->tgl_keluar < $barangMasukTerawal->tgl_masuk) {
            return redirect()->back()->with('error', 'Tanggal keluar tidak boleh lebih kecil dari tanggal masuk pertama.');
        }

        // Periksa apakah stok cukup
        if ($barang->stok < $request->qty_keluar) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi. Proses gagal.');
        }

        // Proses menyimpan data barang ke tabel 'barangkeluar'
        BarangKeluar::create($request->all());

        // Kurangi stok barang
        $barang->stok -= $request->qty_keluar;
        $barang->save();

        return redirect()->route('barangkeluar.index')->with('success', 'Data Barang Keluar berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetBarangKeluar = BarangKeluar::find($id);

        return view('v_barangkeluar.show', compact('rsetBarangKeluar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barangkeluar = BarangKeluar::findOrFail($id);
        $aBarang = Barang::select('id', \DB::raw("CONCAT(merk, ' - ', seri) AS merkseri"))
        ->pluck('merkseri', 'id');

        return view('v_barangkeluar.edit', compact('barangkeluar', 'aBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_keluar' => 'required|date',
            'qty_keluar' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barangkeluar = BarangKeluar::findOrFail($id);
        $barang = Barang::findOrFail($request->barang_id);

        // Periksa apakah tanggal keluar lebih kecil dari tanggal masuk
        $barangMasukTerawal = BarangMasuk::where('barang_id', $request->barang_id)->orderBy('tgl_masuk', 'asc')->first();
        if ($barangMasukTerawal && $request->tgl_keluar < $barangMasukTerawal->tgl_masuk) {
            return redirect()->back()->with('error', 'Tanggal tidak valid. Tanggal keluar tidak boleh sebelum tanggal masuk..');
        }

        // Hitung stok baru jika qty_keluar diperbarui
        $stokBaru = $barang->stok + $barangkeluar->qty_keluar - $request->qty_keluar;

        if ($stokBaru < 0) {
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi. Proses gagal.');
        }

        // Update data barang keluar
        $barangkeluar->update($request->all());

        // Update stok barang
        $barang->stok = $stokBaru;
        $barang->save();

        return redirect()->route('barangkeluar.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $rsetBarangKeluar = BarangKeluar::find($id);

        //delete post
        $rsetBarangKeluar->delete();

        //redirect to index
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
