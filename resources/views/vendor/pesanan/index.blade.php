@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-info text-white me-2">
            <i class="mdi mdi-clipboard-check"></i>
        </span>
        Pesanan Lunas
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pesanan Lunas</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        Daftar Pesanan Lunas - <span class="text-primary">{{ session('vendor_nama') }}</span>
                    </h4>
                    <span class="badge bg-success fs-6">
                        {{ $pesanan->count() }} Pesanan
                    </span>
                </div>

                @if($pesanan->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-clipboard-text-off" style="font-size: 56px; color: #ccc;"></i>
                        <p class="text-muted mt-3">Belum ada pesanan yang lunas.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ID Pesanan</th>
                                    <th>Nama Customer</th>
                                    <th>Detail Menu</th>
                                    <th>Total</th>
                                    <th>Waktu Pesan</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold">#{{ $p->idpesanan }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width:32px; height:32px; font-size:12px;">
                                                {{ strtoupper(substr($p->nama, 0, 1)) }}
                                            </span>
                                            {{ $p->nama }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $p->items }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        Rp {{ number_format($p->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check me-1"></i>Lunas
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection