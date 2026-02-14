@extends('layout.main')

@section('content')
<h1>Tambah Buku</h1>

<form action="{{ route('buku.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="idkategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($kategori as $k)
                <option value="{{ $k->idkategori }}">
                    {{ $k->nama_kategori }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Kode Buku</label>
        <input type="text" name="kode" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Pengarang</label>
        <input type="text" name="pengarang" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('buku.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
