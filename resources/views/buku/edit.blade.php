@extends('layout.main')

@section('content')
<h1>Edit Buku</h1>

<form action="{{ route('buku.update', $buku->idbuku) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="idkategori" class="form-control" required>
            @foreach($kategori as $k)
                <option value="{{ $k->idkategori }}"
                    {{ $buku->idkategori == $k->idkategori ? 'selected' : '' }}>
                    {{ $k->nama_kategori }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Kode Buku</label>
        <input type="text" name="kode" class="form-control"
               value="{{ $buku->kode }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control"
               value="{{ $buku->judul }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Pengarang</label>
        <input type="text" name="pengarang" class="form-control"
               value="{{ $buku->pengarang }}" required>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="{{ route('buku.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
