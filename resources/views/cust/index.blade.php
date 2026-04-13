@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h3>Data Customer</h3>

    <a href="/customer/create1" class="btn btn-primary mb-3">Tambah Customer 1</a>
    <a href="/customer/create2" class="btn btn-success mb-3">Tambah Customer 2</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Kecamatan</th>
                <th>Kode Pos</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer as $c)
            <tr>
                <td>{{ $c->nama }}</td>
                <td>{{ $c->alamat }}</td>
                <td>{{ $c->provinsi }}</td>
                <td>{{ $c->kota }}</td>
                <td>{{ $c->kecamatan }}</td>
                <td>{{ $c->kodepos }}</td>
                <td>
                    @if($c->foto)
                        <img src="data:image/png;base64,{{ base64_encode(stream_get_contents($c->foto)) }}" width="80">
                    @elseif($c->foto_path)
                        <img src="{{ asset('uploads/'.$c->foto_path) }}" width="80">
                    @endif
                </td>
                <td>
                    <form action="/customer/{{ $c->id }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection