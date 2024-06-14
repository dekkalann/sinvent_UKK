@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
               <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>ID</td>
                                <td>{{ $rsetBarangMasuk->id }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Masuk</td>
                                <td>{{ $rsetBarangMasuk->tgl_masuk }}</td>
                            </tr>
                            <tr>
                                <td>QTY Masuk</td>
                                <td>{{ $rsetBarangMasuk->qty_masuk }}</td>
                            </tr>
                            <tr>
                                <td>Merk</td>
                                <td>{{ $rsetBarangMasuk->barang->merk }}</td>
                            </tr>
                            <tr>
                                <td>Seri</td>
                                <td>{{ $rsetBarangMasuk->barang->seri }}</td>
                            </tr>
                        </table>
                    </div>
               </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12  text-center">
                

                <a href="{{ route('barang.index') }}" class="btn btn-md btn-primary mb-3">Back</a>
            </div>
        </div>
    </div>
@endsection