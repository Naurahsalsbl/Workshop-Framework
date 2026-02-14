@extends('layout.main')

@section('content')
<h1>Daftar Kategori</h1>

<a href="{{ route('kategori.create') }}" class="btn btn-primary mb-3">Tambah Kategori</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID Kategori</th>
            <th>Nama Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($kategori as $k)
        <tr>
            <td>{{ $k->idkategori }}</td>
            <td>{{ $k->nama_kategori }}</td>
            <td>
                <a href="{{ route('kategori.edit', $k->idkategori) }}" class="btn btn-warning btn-sm">Edit</a>
                
                <form action="{{ route('kategori.destroy', $k->idkategori) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3">Belum ada kategori</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
