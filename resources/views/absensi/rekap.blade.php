@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-table"></i>
        </span>
        Rekap Absensi
    </h3>
    <a href="{{ route('absensi.scan') }}" class="btn btn-gradient-primary btn-sm">
        <i class="mdi mdi-nfc me-1"></i>Scan NFC
    </a>
</div>

<div class="card grid-margin">
    <div class="card-body">
        <h4 class="card-title mb-3">Filter Absensi</h4>
        <form method="GET" action="{{ route('absensi.rekap') }}" class="row g-3">
            <div class="col-md-5">
                <label class="text-muted mb-1" style="font-size:13px;">Matakuliah</label>
                <select name="matakuliah_id" class="form-control">
                    <option value="">-- Pilih Matakuliah --</option>
                    @foreach($matakuliah as $mk)
                        <option value="{{ $mk->id }}" {{ $matakuliah_id == $mk->id ? 'selected' : '' }}>
                            {{ $mk->kode }} - {{ $mk->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="text-muted mb-1" style="font-size:13px;">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-gradient-primary w-100">
                    <i class="mdi mdi-magnify me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card grid-margin">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">
                Daftar Absensi
                <span class="badge bg-primary ms-2">{{ count($absensi) }}</span> mahasiswa
            </h4>
        </div>

        @if(count($absensi) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No.</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Matakuliah</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensi as $i => $a)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $a->nim }}</td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->matkul }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->waktu_absen)->format('H:i:s') }}</td>
                        <td>
                            @if($a->status === 'hadir')
                                <span class="badge bg-success">Hadir</span>
                            @else
                                <span class="badge bg-warning text-dark">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted text-center py-4">
                @if($matakuliah_id)
                    Belum ada absensi pada tanggal {{ $tanggal }}.
                @else
                    Pilih matakuliah dan tanggal untuk melihat rekap.
                @endif
            </p>
        @endif
    </div>
</div>
@endsection