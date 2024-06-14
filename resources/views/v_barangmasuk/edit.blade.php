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
                        <form action="{{ route('barangmasuk.update', $barangmasuk->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="font-weight-bold">TANGGAL MASUK</label>
                                <input type="date" class="form-control @error('tgl_masuk') is-invalid @enderror" name="tgl_masuk" value="{{ old('tgl_masuk', $barangmasuk->tgl_masuk) }}" placeholder="Masukkan Merk Barang">
                            
                                <!-- error message untuk nama -->
                                @error('tgl_masuk')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">QTY MASUK</label>
                                <input type="number" class="form-control @error('qty_masuk') is-invalid @enderror" name="qty_masuk" value="{{ old('qty_masuk', $barangmasuk->qty_masuk) }}" placeholder="Masukkan Jumlah Barang">
                            
                                <!-- error message untuk nis -->
                                @error('qty_masuk')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- <div class="form-group">
                                <label class="font-weight-bold">BARANG</label>
    
                                <div class="form-check">
                                    <select class="form-select" name="barang_id" aria-label="Default select example">
                                        @foreach($aBarang as $id => $barang)
                                            <option value="{{ $id }}" {{ old('barang_id', $barangmasuk->barang_id) == $id ? 'selected' : '' }}>
                                                {{ $barang }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                            
                                                     

                            <button type="submit" class="btn btn-md btn-primary">UPDATE</button>
                            <a href="{{ route('barangmasuk.index') }}" class="btn btn-md btn-secondary">BATAL</a>

                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
