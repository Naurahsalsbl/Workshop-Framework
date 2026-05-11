@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-store"></i>
        </span>
        Kunjungan Toko
    </h3>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">

    {{-- LIST TOKO --}}
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">List Toko</h4>
                    <div class="d-flex gap-2">
                    <a href="{{ route('toko.scanner') }}"
                       class="btn btn-gradient-success btn-sm">
                        <i class="mdi mdi-qrcode-scan me-1"></i>
                        Scan Kunjungan
                    </a>
                    <a href="{{ route('toko.create') }}"
                       class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i>
                        Tambah Toko
                    </a>
                </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Toko</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Accuracy</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($toko as $t)
                        <tr>
                            <td><small class="text-muted">{{ $t->barcode }}</small></td>
                            <td>{{ $t->nama_toko }}</td>
                            <td>{{ $t->latitude ?? '-' }}</td>
                            <td>{{ $t->longitude ?? '-' }}</td>
                            <td>
                                @if($t->accuracy)
                                    <span class="badge bg-success">{{ $t->accuracy }} m</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum diset</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm mb-1"
                                    onclick="ambilTitikAwal({{ $t->id }}, '{{ $t->nama_toko }}')">
                                    <i class="mdi mdi-crosshairs-gps"></i> Titik Awal
                                </button>
                                <a href="{{ route('toko.cetak-barcode', $t->id) }}"
                                   class="btn btn-secondary btn-sm mb-1" target="_blank">
                                    <i class="mdi mdi-barcode"></i> Cetak
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

{{-- Modal Titik Awal --}}
<div class="modal fade" id="modalTitikAwal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Titik Awal Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted" style="font-size:13px;">
                    Klik tombol di bawah untuk mengambil koordinat GPS lokasi toko saat ini.
                    Sistem akan mencari posisi dengan akurasi terbaik.
                </p>

                <div id="modalLoading" class="text-center d-none mb-3">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size:13px;">Mengambil posisi terbaik...</p>
                </div>

                <div id="infoPosisi" class="bg-light rounded p-3 mb-3 d-none">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Latitude</span>
                        <span id="modal-lat" class="fw-bold"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Longitude</span>
                        <span id="modal-lng" class="fw-bold"></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Accuracy</span>
                        <span id="modal-acc" class="fw-bold text-success"></span>
                    </div>
                </div>

                <button id="btnAmbilPosisi" class="btn btn-gradient-primary w-100">
                    <i class="mdi mdi-crosshairs-gps me-1"></i>Ambil Posisi Sekarang
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="btnSimpanTitik" class="btn btn-gradient-success d-none">
                    <i class="mdi mdi-check me-1"></i>Simpan Titik Awal
                </button>
            </div>
        </div>
    </div>
</div>

<script>

// ─── Variabel global ───────────────────────────────────────────────
let selectedTokoId  = null;
let posisiTitikAwal = null;

// ─── Geolocation akurat ────────────────────────────────────────────
function getAccuratePosition(targetAccuracy = 100, maxWait = 8000) {

    return new Promise((resolve, reject) => {

        let bestResult = null;
        const startTime = Date.now();

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }

                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);

                    if (bestResult) {
                        resolve(bestResult);
                    } else {
                        reject(new Error("Timeout, tidak dapat posisi"));
                    }
                }
            },

            (error) => reject(error),

            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: maxWait
            }
        );
    });
}

// ─── Modal Titik Awal ──────────────────────────────────────────────
function ambilTitikAwal(id, nama) {

    selectedTokoId  = id;
    posisiTitikAwal = null;

    document.getElementById('infoPosisi')
        .classList.add('d-none');

    document.getElementById('btnSimpanTitik')
        .classList.add('d-none');

    document.getElementById('modalLoading')
        .classList.add('d-none');

    document.getElementById('btnAmbilPosisi')
        .disabled = false;

    document.getElementById('btnAmbilPosisi')
        .innerHTML =
        '<i class="mdi mdi-crosshairs-gps me-1"></i>Ambil Posisi Sekarang';

    document.querySelector('#modalTitikAwal .modal-title')
        .textContent = 'Titik Awal: ' + nama;

    new bootstrap.Modal(
        document.getElementById('modalTitikAwal')
    ).show();
}

// ─── Ambil posisi GPS ──────────────────────────────────────────────
document.getElementById('btnAmbilPosisi')
    .addEventListener('click', async function () {

    this.disabled = true;

    this.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1"></span>Mengambil posisi...';

    document.getElementById('modalLoading')
        .classList.remove('d-none');

    document.getElementById('infoPosisi')
        .classList.add('d-none');

    document.getElementById('btnSimpanTitik')
        .classList.add('d-none');

    try {

        const pos = await getAccuratePosition(100, 8000);

        posisiTitikAwal = pos.coords;

        document.getElementById('modal-lat')
            .textContent = pos.coords.latitude;

        document.getElementById('modal-lng')
            .textContent = pos.coords.longitude;

        document.getElementById('modal-acc')
            .textContent =
            pos.coords.accuracy.toFixed(1) + ' m';

        document.getElementById('infoPosisi')
            .classList.remove('d-none');

        document.getElementById('btnSimpanTitik')
            .classList.remove('d-none');

        this.innerHTML =
            '<i class="mdi mdi-check me-1"></i>Posisi Didapat';

    } catch (e) {

        alert('Gagal ambil posisi: ' + e.message);

        this.disabled = false;

        this.innerHTML =
            '<i class="mdi mdi-crosshairs-gps me-1"></i>Coba Lagi';

    } finally {

        document.getElementById('modalLoading')
            .classList.add('d-none');
    }
});

// ─── Simpan titik awal ─────────────────────────────────────────────
document.getElementById('btnSimpanTitik')
    .addEventListener('click', function () {

    if (!posisiTitikAwal) return;

    this.disabled = true;

    this.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

    fetch(`/${selectedTokoId}/titik-awal`, {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
        },

        body: JSON.stringify({

            latitude: posisiTitikAwal.latitude,

            longitude: posisiTitikAwal.longitude,

            accuracy: posisiTitikAwal.accuracy,
        })
    })

    .then((res) => res.json())

    .then(() => {

        bootstrap.Modal
            .getInstance(
                document.getElementById('modalTitikAwal')
            )
            .hide();

        this.disabled = false;

        this.innerHTML =
            '<i class="mdi mdi-check me-1"></i>Simpan Titik Awal';

        setTimeout(() => {
            location.reload();
        }, 300);

    })

    .catch(() => {

        alert('Gagal menyimpan titik awal!');

        this.disabled = false;

        this.innerHTML =
            '<i class="mdi mdi-check me-1"></i>Simpan Titik Awal';

    });
});

</script>
@endsection