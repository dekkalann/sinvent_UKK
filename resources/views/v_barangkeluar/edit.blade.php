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
                    <div class="card-body">
                        <form action="{{ route('barangkeluar.update', $barangkeluar->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="font-weight-bold">TANGGAL keluar</label>
                                <input type="date" class="form-control @error('tgl_keluar') is-invalid @enderror" name="tgl_keluar" value="{{ old('tgl_keluar', $barangkeluar->tgl_keluar) }}" placeholder="Masukkan Merk Barang">
                            
                                <!-- error message untuk nama -->
                                @error('tgl_keluar')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">QTY keluar</label>
                                <input type="number" class="form-control @error('qty_keluar') is-invalid @enderror" name="qty_keluar" value="{{ old('qty_keluar', $barangkeluar->qty_keluar) }}" placeholder="Masukkan Jumlah Barang">
                            
                                <!-- error message untuk nis -->
                                @error('qty_keluar')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">BARANG</label>
    
                                <div class="form-check">
                                    <select class="form-select" name="barang_id" aria-label="Default select example">
                                        @foreach($aBarang as $id => $barang)
                                            <option value="{{ $id }}" {{ old('barang_id', $barangkeluar->barang_id) == $id ? 'selected' : '' }}>
                                                {{ $barang }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                                                     

                            <button type="submit" class="btn btn-md btn-primary">UPDATE</button>
                            <a href="{{ route('barangkeluar.index') }}" class="btn btn-md btn-secondary">BATAL</a>

                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
