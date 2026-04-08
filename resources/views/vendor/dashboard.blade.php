@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-view-dashboard"></i>
        </span>
        Dashboard Vendor
    </h3>
</div>

<div class="row">
    {{-- Welcome Card --}}
    <div class="col-12 grid-margin">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Selamat Datang, di {{ session('vendor_nama') }}! 👋</h4>
                    <p class="mb-0" style="opacity: 0.85;">Kelola menu dan pantau pesanan dari sini.</p>
                </div>
                <i class="mdi mdi-store" style="font-size: 64px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Menu Card --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <span class="bg-gradient-success p-3 rounded-circle d-inline-flex">
                        <i class="mdi mdi-food text-white" style="font-size: 32px;"></i>
                    </span>
                </div>
                <h5 class="fw-bold">Master Menu</h5>
                <p class="text-muted">Tambah dan kelola daftar menu kantin kamu</p>
                <a href="{{ route('vendor.menu.index') }}" class="btn btn-gradient-success">
                    <i class="mdi mdi-arrow-right me-1"></i>Kelola Menu
                </a>
            </div>
        </div>
    </div>

    {{-- Pesanan Card --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <span class="bg-gradient-info p-3 rounded-circle d-inline-flex">
                        <i class="mdi mdi-clipboard-list text-white" style="font-size: 32px;"></i>
                    </span>
                </div>
                <h5 class="fw-bold">Pesanan Masuk</h5>
                <p class="text-muted">Lihat pesanan yang sudah lunas dibayar</p>
                <a href="{{ route('vendor.pesanan.index') }}" class="btn btn-gradient-info">
                    <i class="mdi mdi-arrow-right me-1"></i>Lihat Pesanan
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="{{ route('vendor.logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger">
                <i class="mdi mdi-logout me-1"></i>Logout
            </button>
        </form>
    </div>
</div>
@endsection