@extends('layout.main')

@section('content')
<div class="container">

    <h3>Edit Barang</h3>

    <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" 
                   name="nama_barang" 
                   class="form-control"
                   value="{{ $barang->nama_barang }}" 
                   required>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" 
                   name="harga" 
                   class="form-control"
                   value="{{ $barang->harga }}" 
                   required>
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number" 
                   name="stok" 
                   class="form-control"
                   value="{{ $barang->stok }}" 
                   required>
        </div>

        <button type="submit" class="btn btn-success">
            Update
        </button>

        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>
@endsection