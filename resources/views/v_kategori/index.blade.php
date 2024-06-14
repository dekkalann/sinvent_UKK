<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
@extends('layouts.adm-main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            <div class="card">
                    @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    @if(Session::has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('kategori.create') }}" class="btn btn-md btn-success mb-3 mr-2">TAMBAH KATEGORI</a>
                    <form class="form-inline" action="{{ route('kategori.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2" name="search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>KATEGORI</th>
                            <th>KETERANGAN</th>
                            <th>DESKRIPSI</th>                       
                            <th style="width: 15%">AKSI</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rsetKategori as $rowkategori)
                            <tr>
                                <td>{{ $rowkategori->id }}</td>
                                <td>{{ $rowkategori->kategori }}</td>
                                <td>{{ $rowkategori->ketKategori }}</td>
                                <td>{{ $rowkategori->deskripsi }}</td>
                                <td class="text-center"> 
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('kategori.destroy', $rowkategori->id) }}" method="POST">
                                        <a href="{{ route('kategori.show', $rowkategori->id) }}" class="btn btn-sm btn-dark"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('kategori.edit', $rowkategori->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-alt"></i></a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Data Kategori belum tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- {{ $kategori->links() }} --}}

            </div>
        </div>
    </div>
@endsection
</body>
</html>
