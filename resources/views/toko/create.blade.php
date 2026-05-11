@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-store-plus"></i>
        </span>
        Tambah Toko
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('toko.index') }}">Kunjungan Toko</a></li>
            <li class="breadcrumb-item active">Tambah Toko</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Form Tambah Toko</h4>
                <form method="POST" action="{{ route('toko.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="text-muted">Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" placeholder="Contoh: Toko Maju Jaya" required>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary w-50">
                            <i class="mdi mdi-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-gradient-primary w-50">
                            <i class="mdi mdi-check me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection