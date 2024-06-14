<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Barang;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // $query = $request->input('query');
    //     // $rsetKategori = Kategori::get();
    //     // return view('v_kategori.index', compact('rsetKategori'));
    //     $aKategori = array('blank'=>'Pilih Kategori',
    //                         'M'=>'Barang Modal',
    //                         'A'=>'Alat',
    //                         'BHP'=>'Bahan Habis Pakai',
    //                         'BTHP'=>'Bahan Tidak Habis Pakai'
    //                         );
    //     $rsetKategori = Kategori::orderBy('id', 'asc')->paginate(30);
    //     return view('v_kategori.index', compact('rsetKategori','aKategori'));

    // }

    // public function index()
    // {
    //     $rsetKategori = Kategori::getKategoriAll();
    //     return view ('v_kategori.index', compact('rsetKategori'));
    //     //return view("layouts/main");
      
    // }

    // public function index(Request $request)
    // {
    //     // Ambil inputan search dari request
    //     $search = $request->input('search');
        
    //     // Buat query untuk pencarian
    //     $query = Kategori::query();

    //     if ($search) {
    //         $query->where('id', 'like', '%' . $search . '%')
    //             ->orWhere('kategori', 'like', '%' . $search . '%')
    //             ->orWhere(DB::raw('ketKategori(kategori)'), 'like', '%' . $search . '%')
    //             ->orWhere('deskripsi', 'like', '%' . $search . '%');
    //     }

    //     // Dapatkan hasil query dengan memanggil getKategoriAll
    //     $rsetKategori = Kategori::getKategoriAll($query);
    //     // $rsetKategori = $query->get();

    //     return view('v_kategori.index', compact('rsetKategori'));
    // }

    // public function index(Request $request) //eloquent
    // {
    //     $search = $request->input('search');

    //     $query = Kategori::select('id', 'deskripsi', 'kategori',
    //         DB::raw('(CASE
    //             WHEN kategori = "M" THEN "Modal"
    //             WHEN kategori = "A" THEN "Alat"
    //             WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
    //             ELSE "Bahan Tidak Habis Pakai"
    //             END) AS ketKategori'));

    //     if ($search) {
    //         $query->where('deskripsi', 'like', '%' . $search . '%')
    //             ->orWhere('kategori', 'like', '%' . $search . '%')
    //             ->orWhere(DB::raw('(CASE
    //             WHEN kategori = "M" THEN "Modal"
    //             WHEN kategori = "A" THEN "Alat"
    //             WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
    //             ELSE "Bahan Tidak Habis Pakai"
    //             END)'), 'like', '%' . $search . '%');
    //     }

    //     $rsetKategori = $query->paginate(100);

    //     return view ('v_kategori.index', compact('rsetKategori', 'search'));
    // }

    
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Pilih kolasi yang konsisten, misalnya 'utf8mb4_unicode_ci'
        $collation = 'utf8mb4_unicode_ci';

        $query = DB::table('kategori')
            ->select('id', 'deskripsi', 'kategori', DB::raw('ketKategori(kategori) AS ketKategori'));

        if ($search) {
            $query->where(DB::raw('deskripsi COLLATE ' . $collation), 'like', '%' . $search . '%')
                ->orWhere(DB::raw('kategori COLLATE ' . $collation), 'like', '%' . $search . '%')
                ->orWhere(DB::raw('ketKategori(kategori) COLLATE ' . $collation), 'like', '%' . $search . '%');
        }

        $rsetKategori = $query->get();

        return view('v_kategori.index', compact('rsetKategori', 'search'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aKategori = [
            'M' => 'Barang Modal',
            'A' => 'Alat',
            'BHP' => 'Bahan Habis Pakai',
            'BTHP' => 'Bahan Tidak Habis Pakai'
        ];
    
        // Tambahkan opsi Pilih Jenis ke array
        $aKategori = $aKategori;
    
        return view('v_kategori.create', compact('aKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input sesuai kebutuhan Anda
        $request->validate([
            'deskripsi' => 'required',
            'kategori' => 'required',
        ]);

        // Proses menyimpan data barang ke tabel 'barang'
        Kategori::create($request->all());

        // Redirect to index
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Menggunakan query untuk mendapatkan kategori berdasarkan ID
        $rsetKategori = Kategori::getKategoriAll()->where('id', $id)->first();

        // Jika kategori tidak ditemukan, tampilkan halaman 404
        if (!$rsetKategori) {
            abort(404, 'Kategori tidak ditemukan');
        }

        // Return view dengan data kategori yang ditemukan
        return view('v_kategori.show', compact('rsetKategori'));
    }

    // public function show(string $id)
    // {
    //     $rsetKategori = DB::select('call getKategoriById(?)',[$id]);
        
    //     dd($rsetKategori);
    //     return view('v_kategori.show', compact('rsetKategori'));
    // }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $kategori = Kategori::findOrFail($id);
    $aKategori = [
        'blank' => 'Pilih Kategori',
        'M' => 'Barang Modal',
        'A' => 'Alat',
        'BHP' => 'Bahan Habis Pakai',
        'BTHP' => 'Bahan Tidak Habis Pakai'
    ];

    // Tambahkan opsi Pilih Jenis ke array
    $aKategori = ['blank' => 'Pilih Kategori'] + $aKategori;

    // Debugging
    // dd($kategori);

    return view('v_kategori.edit', compact('kategori', 'aKategori'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required',
            'deskripsi' => 'required',
        ]);

        $kategori = Kategori::findOrFail($id);

        // // Debugging
        // dd($request->all(), $kategori);


        $kategori->update($request->all());

        return redirect()->route('kategori.index')->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        

            // $rsetKategori = Kategori::find($id);

            // $rsetKategori->delete();

            // return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);

            $kategori = Kategori::findOrFail($id);
            $isUsedInBarang = Barang::where('kategori_id', $id)->exists();

            if ($isUsedInBarang) {
                return redirect()->route('kategori.index')->with('error', 'Kategori tidak dapat dihapus karena sedang digunakan di barang.');
            }

            $kategori->delete();
            return redirect()->route('kategori.index')->with('success', 'Kategori Berhasil Dihapus!');

        // }

    }

    

    public function search(Request $request)
    {
        $query = $request->input('query');
        $rsetKategori = Kategori::where ('kategori', 'like', "%{$query}%")
                                ->orWhere('deskripsi', 'like', "%{$query}%");
        return view('v_kategori.index', compact('rsetKategori'));
    }

    public function getAPIKategori(){
        $rsetKategori = Kategori::getKategoriAll();
        $data = array("data"=>$rsetKategori);
        return response()->json($data);
    }

    // function untuk membuat index api
    function showAPIKategori(Request $request){
        $kategori = Kategori::all();
        return response()->json($kategori);
    }

    // function untuk create api
    function buatAPIKategori(Request $request){
        $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);

        // Simpan data kategori
        $kat = Kategori::create([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]);

        return response()->json(["status"=>"data berhasil dibuat"]);
    }

    public function showoneAPIKategori($id)
    {
        // Temukan kategori berdasarkan ID
        $kategori = Kategori::find($id);

        // Periksa apakah kategori ditemukan
        if ($kategori) {
            return response()->json([
                'success' => true,
                'data' => $kategori
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }

     // function untuk delete api
     function hapusAPIKategori($kategori_id){

        $del_kategori = Kategori::findOrFail($kategori_id);
        $del_kategori -> delete();

        return response()->json(["status"=>"data berhasil dihapus"]);
    }

    function updateAPIKategori(Request $request, $kategori_id){
        $kategori = Kategori::find($kategori_id);

        if (null == $kategori){
            return response()->json(['status'=>"kategori tidak ditemukan"]);
        }

         $kategori->deskripsi= $request->deskripsi;
         $kategori->kategori = $request->kategori;
         $kategori->save();

        return response()->json(["status"=>"kategori berhasil diubah"]);
    }
}
