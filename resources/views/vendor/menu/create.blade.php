@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
            <i class="mdi mdi-plus-circle"></i>
        </span>
        Tambah Menu
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.menu.index') }}">Master Menu</a></li>
            <li class="breadcrumb-item active">Tambah Menu</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Form Tambah Menu</h4>

                <form method="POST" action="{{ route('vendor.menu.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="nama_menu" class="form-control @error('nama_menu') is-invalid @enderror"
                            placeholder="Contoh: Nasi Goreng Spesial" value="{{ old('nama_menu') }}" required>
                        @error('nama_menu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror"
                                placeholder="15000" value="{{ old('harga') }}" min="0" required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Gambar Menu <span class="text-muted">(opsional)</span></label>
                        <input type="file" name="path_gambar" class="form-control" accept="image/*"
                            onchange="previewImage(this)">
                        <div id="imagePreview" class="mt-2" style="display:none;">
                            <img id="previewImg" src="" alt="Preview"
                                style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient-success flex-grow-1">
                            <i class="mdi mdi-content-save me-1"></i>Simpan Menu
                        </button>
                        <a href="{{ route('vendor.menu.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection