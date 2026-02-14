@extends('layout.main')

@section('content')
<h1>Daftar Buku</h1>

<a href="{{ route('buku.create') }}" class="btn btn-primary mb-3">Tambah Buku</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID Buku</th>
            <th>Kategori</th>
            <th>Kode</th>
            <th>Judul</th>
            <th>Pengarang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($buku as $b)
        <tr>
            <td>{{ $b->idbuku }}</td>
            <td>{{ $b->nama_kategori }}</td>
            <td>{{ $b->kode }}</td>
            <td>{{ $b->judul }}</td>
            <td>{{ $b->pengarang }}</td>
            <td>
                <a href="{{ route('buku.edit', $b->idbuku) }}" class="btn btn-warning btn-sm">Edit</a>
                
                <form action="{{ route('buku.destroy', $b->idbuku) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">Belum ada buku</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
