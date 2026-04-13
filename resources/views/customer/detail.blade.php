@extends('layout.main')

@section('content')
<div class="container mt-4">

    <h3>Detail Pesanan</h3>

    <div class="card mt-3">
        <div class="card-body">

            <p><b>ID Pesanan:</b> {{ $pesanan->idpesanan }}</p>
            <p><b>Nama:</b> {{ $pesanan->nama }}</p>
            <p><b>Total:</b> Rp {{ number_format($pesanan->total, 0, ',', '.') }}</p>

            <p><b>Status:</b>
                @if($pesanan->status_bayar == 1)
                    <span style="color: green;">Lunas</span>
                @elseif($pesanan->status_bayar == 0)
                    <span style="color: orange;">Belum Bayar</span>
                @else
                    <span style="color: red;">Gagal</span>
                @endif
            </p>

        </div>
    </div>

</div>
@endsection