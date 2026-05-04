@extends('layout.main')

@section('content')

<div class="container">

    <h3>Data Barang</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('barang.create') }}" class="btn btn-primary mb-3">
        Tambah Barang
    </a>

    <!-- FORM CETAK LABEL -->
    <form id="formCetak" method="POST" action="{{ route('cetak.label') }}">
        @csrf

        <div class="mb-3">
            <label>Koordinat X (1-5)</label>
            <input type="number" name="x" min="1" max="5" required>

            <label class="ms-3">Koordinat Y (1-8)</label>
            <input type="number" name="y" min="1" max="8" required>

            <button type="submit" class="btn btn-success ms-3">
                Cetak Label
            </button>
        </div>
    </form>
    <!-- END FORM CETAK -->

    <!-- TABEL DI LUAR FORM -->
    <table id="tabelBarang" class="table table-bordered">
        <thead>
            <tr>
                <th>Pilih</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang as $b)
            <tr id="row-{{ $b->id_barang }}">
                <td>
                    <input type="checkbox" form="formCetak" name="pilih[]" value="{{ $b->id_barang }}">
                </td>
                <td>{{ $b->id_barang }}</td>
                <td>{{ $b->nama }}</td>
                <td>Rp {{ number_format($b->harga,0,',','.') }}</td>
                <td>
                    <a href="{{ route('barang.edit',$b->id_barang) }}" class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <form action="{{ route('barang.destroy',$b->id_barang) }}" 
                          method="POST" 
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin hapus?')">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection