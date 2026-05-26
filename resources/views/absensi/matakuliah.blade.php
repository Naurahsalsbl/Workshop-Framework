@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-book-open-variant"></i>
        </span>
        Data Matakuliah
    </h3>
    <a href="{{ route('absensi.scan') }}" class="btn btn-gradient-primary btn-sm">
        <i class="mdi mdi-nfc me-1"></i>Scan NFC
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- Form tambah --}}
    <div class="col-lg-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Tambah Matakuliah</h4>
                <form method="POST" action="{{ route('absensi.matakuliah.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="text-muted mb-1" style="font-size:13px;">Kode</label>
                        <input type="text" name="kode" class="form-control" placeholder="Contoh: TIF101" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1" style="font-size:13px;">Nama Matakuliah</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama matakuliah" required>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted mb-1" style="font-size:13px;">Dosen Pengampu</label>
                        <input type="text" name="dosen" class="form-control" placeholder="Nama dosen" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary w-100">
                        <i class="mdi mdi-plus me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="col-lg-8 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">
                    Daftar Matakuliah
                    <span class="badge bg-primary ms-2">{{ count($matakuliah) }}</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Matakuliah</th>
                                <th>Dosen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matakuliah as $mk)
                            <tr>
                                <td><span class="badge bg-primary">{{ $mk->kode }}</span></td>
                                <td>{{ $mk->nama }}</td>
                                <td>{{ $mk->dosen }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada matakuliah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection