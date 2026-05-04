@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-credit-card"></i>
        </span>
        Pembayaran
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/order">Pemesanan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pembayaran</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">
                    <i class="mdi mdi-receipt text-primary me-2"></i>Detail Pesanan
                </h4>

                {{-- Info Pesanan --}}
                <div class="bg-light rounded p-3 mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nama Customer</span>
                        <span class="fw-bold">{{ $pesanan->nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID Pesanan</span>
                        <span class="fw-bold">#{{ $pesanan->idpesanan }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Pembayaran</span>
                        <span class="fw-bold text-primary fs-5">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <span>
                            @if($pesanan->status_bayar == 0)
                                <span class="badge bg-warning text-dark">
                                    <i class="mdi mdi-clock-outline me-1"></i>Menunggu Pembayaran
                                </span>
                            @elseif($pesanan->status_bayar == 1)
                                <span class="badge bg-success">
                                    <i class="mdi mdi-check-circle me-1"></i>Lunas
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="mdi mdi-close-circle me-1"></i>Gagal
                                </span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($pesanan->status_bayar == 0)
                    {{-- Metode Pembayaran --}}
                    <div class="text-center mb-4">
                        <p class="text-muted mb-3">Pilih metode pembayaran yang tersedia:</p>
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <div class="border rounded p-2 text-center" style="width: 80px;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/200px-Bank_Central_Asia.svg.png"
                                    height="30" alt="BCA" onerror="this.style.display='none'">
                                <small class="d-block text-muted mt-1">BCA VA</small>
                            </div>
                            <div class="border rounded p-2 text-center" style="width: 80px;">
                                <i class="mdi mdi-qrcode" style="font-size: 30px; color: #333;"></i>
                                <small class="d-block text-muted mt-1">QRIS</small>
                            </div>
                            <div class="border rounded p-2 text-center" style="width: 80px;">
                                <i class="mdi mdi-wallet" style="font-size: 30px; color: #00AED6;"></i>
                                <small class="d-block text-muted mt-1">GoPay</small>
                            </div>
                        </div>
                    </div>

                    <button id="btnBayar" class="btn btn-gradient-primary btn-lg w-100">
                        <i class="mdi mdi-lock me-2"></i>Bayar Sekarang
                        <small class="d-block" style="font-size: 11px; opacity: 0.8;">Powered by Midtrans</small>
                    </button>

                @elseif($pesanan->status_bayar == 1)
                    <div class="text-center py-3">
                        <i class="mdi mdi-check-circle-outline text-success" style="font-size: 64px;"></i>
                        <h5 class="mt-3 text-success fw-bold">Pembayaran Berhasil!</h5>
                        <p class="text-muted">Pesanan kamu sedang diproses oleh vendor.</p>
                        <a href="/order" class="btn btn-gradient-primary mt-2">
                            <i class="mdi mdi-cart me-1"></i>Pesan Lagi
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="mdi mdi-close-circle-outline text-danger" style="font-size: 64px;"></i>
                        <h5 class="mt-3 text-danger fw-bold">Pembayaran Gagal</h5>
                        <p class="text-muted">Silakan coba pesan kembali.</p>
                        <a href="/order" class="btn btn-gradient-danger mt-2">
                            <i class="mdi mdi-refresh me-1"></i>Coba Lagi
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    @if($pesanan->status_bayar == 0)
    document.getElementById('btnBayar').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<i class="mdi mdi-loading mdi-spin me-2"></i>Memproses...';

            snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){

                // ✅ update status ke database dulu
                fetch('/payment/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        idpesanan: "{{ $pesanan->idpesanan }}"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        // ✅ baru redirect ke halaman QR
                        window.location.href = "/payment/success/{{ $pesanan->idpesanan }}";
                    } else {
                        alert("Gagal update status pembayaran");
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Terjadi error");
                    location.reload();
                });
            },

            onPending: function () {
                location.reload();
            },

            onError: function () {
                alert('Pembayaran gagal, silakan coba lagi.');
                location.reload();
            },

            onClose: function () {
                document.getElementById('btnBayar').disabled = false;
                document.getElementById('btnBayar').innerHTML =
                    '<i class="mdi mdi-lock me-2"></i>Bayar Sekarang' +
                    '<small class="d-block" style="font-size: 11px; opacity: 0.8;">Powered by Midtrans</small>';
            }
        });
    });
    @endif
</script>
@endsection