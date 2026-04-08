@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
            <i class="mdi mdi-food"></i>
        </span>
        Master Menu
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Master Menu</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="mdi mdi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        Daftar Menu - <span class="text-primary">{{ session('vendor_nama') }}</span>
                    </h4>
                    <a href="{{ route('vendor.menu.create') }}" class="btn btn-gradient-success">
                        <i class="mdi mdi-plus me-1"></i>Tambah Menu
                    </a>
                </div>

                @if($menus->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-food-off" style="font-size: 56px; color: #ccc;"></i>
                        <p class="text-muted mt-3">Belum ada menu. Tambah menu pertamamu!</p>
                        <a href="{{ route('vendor.menu.create') }}" class="btn btn-gradient-success">
                            <i class="mdi mdi-plus me-1"></i>Tambah Menu
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Gambar</th>
                                    <th>Nama Menu</th>
                                    <th>Harga</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menus as $i => $menu)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        @if($menu->path_gambar)
                                            <img src="{{ asset('storage/' . $menu->path_gambar) }}"
                                                width="60" height="60"
                                                style="object-fit: cover; border-radius: 8px;"
                                                alt="{{ $menu->nama_menu }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                style="width:60px; height:60px; border-radius:8px;">
                                                <i class="mdi mdi-image-off text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold align-middle">{{ $menu->nama_menu }}</td>
                                    <td class="align-middle text-primary fw-bold">
                                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <form method="POST" action="{{ route('vendor.menu.destroy', $menu->idmenu) }}"
                                            onsubmit="return confirm('Hapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="mdi mdi-delete me-1"></i>Hapus
                                            </button>
                                        </form>
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