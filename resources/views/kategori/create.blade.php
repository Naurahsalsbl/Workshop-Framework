@extends('layout.main')

@section('content')
<h1>Tambah Kategori</h1>

<form action="{{ route('kategori.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nama_kategori" class="form-label">Nama Kategori</label>
        <input type="text" name="nama_kategori" class="form-control" id="nama_kategori" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
