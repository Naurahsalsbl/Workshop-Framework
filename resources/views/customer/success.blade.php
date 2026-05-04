@extends('layout.main')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 text-center p-4" style="max-width: 400px; width: 100%; border-radius: 15px;">
        
        <h2 class="fw-bold text-success mb-3">🎉 Pembayaran Berhasil</h2>

        <div class="mb-3">
            <img src="data:image/png;base64,{{ $qrBase64 }}" class="img-fluid rounded" style="max-width: 150px;">
        </div>

        <div class="text-start">
            <p class="mb-1"><strong>ID Pesanan:</strong> {{ $pesanan->idpesanan }}</p>
            <p class="mb-1"><strong>Nama:</strong> {{ $pesanan->nama }}</p>
            <p class="mb-3"><strong>Total:</strong> 
                <span class="text-primary fw-bold">
                    Rp {{ number_format($pesanan->total, 0, ',', '.') }}
                </span>
            </p>
        </div>

        <!-- Button -->
        <a href="/" class="btn btn-purple w-100 mt-2">Kembali ke Beranda</a>
    </div>
</div>

<script>
    // simpan ID pesanan ke localStorage
    localStorage.setItem("lastPesanan", "{{ $pesanan->idpesanan }}");
</script>
@endsection