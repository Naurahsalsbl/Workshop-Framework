@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-group"></i>
        </span>
        Data Mahasiswa
    </h3>
    <a href="{{ route('absensi.scan') }}" class="btn btn-gradient-primary btn-sm">
        <i class="mdi mdi-nfc me-1"></i>Scan NFC
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- Form tambah mahasiswa --}}
    <div class="col-lg-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Tambah Mahasiswa</h4>
                <form method="POST" action="{{ route('absensi.mahasiswa.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="text-muted mb-1" style="font-size:13px;">NIM</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 2024001" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted mb-1" style="font-size:13px;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama mahasiswa" required>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted mb-1" style="font-size:13px;">Serial NFC (opsional)</label>
                        <input type="text" name="nfc_serial" class="form-control" placeholder="Isi jika kartu sudah ada">
                        <small class="text-muted">Bisa diisi nanti lewat tombol Daftarkan NFC.</small>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary w-100">
                        <i class="mdi mdi-plus me-1"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel mahasiswa --}}
    <div class="col-lg-8 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">
                    Daftar Mahasiswa
                    <span class="badge bg-primary ms-2">{{ count($mahasiswa) }}</span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Serial NFC</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mahasiswa as $m)
                            <tr>
                                <td>{{ $m->nim }}</td>
                                <td>{{ $m->nama }}</td>
                                <td>
                                    @if($m->nfc_serial)
                                        <code style="font-size:11px;">{{ $m->nfc_serial }}</code>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum terdaftar</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info"
                                        onclick="bukaModalNfc({{ $m->id }}, '{{ $m->nama }}')">
                                        <i class="mdi mdi-nfc"></i> Daftarkan NFC
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal daftarkan NFC --}}
<div class="modal fade" id="modalNfc" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftarkan Kartu NFC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Mahasiswa: <strong id="modalNamaMhs"></strong></p>
                <p class="text-muted" style="font-size:13px;">
                    Klik tombol di bawah lalu dekatkan kartu NFC mahasiswa ke HP.
                </p>

                <div id="modalScanArea" class="text-center py-3 d-none">
                    <i class="mdi mdi-nfc-tap text-primary" style="font-size:48px;"></i>
                    <p class="text-primary mt-2">Dekatkan kartu NFC...</p>
                </div>

                <div id="modalSerialBox" class="d-none">
                    <div class="bg-light rounded p-3 text-center">
                        <div class="text-muted" style="font-size:12px;">Serial Number Terdeteksi</div>
                        <code id="modalSerialText" style="font-size:16px; color:#1a73e8;"></code>
                    </div>
                </div>

                <button id="btnScanNfc" class="btn btn-gradient-primary w-100 mt-3" onclick="scanUntukDaftar()">
                    <i class="mdi mdi-nfc me-1"></i>Scan Kartu NFC
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="btnSimpanNfc" class="btn btn-gradient-success d-none" onclick="simpanNfc()">
                    <i class="mdi mdi-check me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken       = document.querySelector('meta[name="csrf-token"]').content;
let selectedMhsId     = null;
let detectedSerial    = null;

function bukaModalNfc(id, nama) {
    selectedMhsId  = id;
    detectedSerial = null;
    document.getElementById('modalNamaMhs').textContent = nama;
    document.getElementById('modalScanArea').classList.add('d-none');
    document.getElementById('modalSerialBox').classList.add('d-none');
    document.getElementById('btnSimpanNfc').classList.add('d-none');
    document.getElementById('btnScanNfc').disabled = false;
    document.getElementById('btnScanNfc').innerHTML = '<i class="mdi mdi-nfc me-1"></i>Scan Kartu NFC';
    new bootstrap.Modal(document.getElementById('modalNfc')).show();
}

async function scanUntukDaftar() {
    if (!('NDEFReader' in window)) {
        alert('Browser tidak mendukung Web NFC. Gunakan Android Chrome.');
        return;
    }

    document.getElementById('btnScanNfc').disabled = true;
    document.getElementById('btnScanNfc').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menunggu kartu...';
    document.getElementById('modalScanArea').classList.remove('d-none');

    try {
        const ndef = new NDEFReader();
        await ndef.scan();

        ndef.addEventListener('reading', ({ serialNumber }) => {
            detectedSerial = serialNumber;
            document.getElementById('modalSerialText').textContent = serialNumber;
            document.getElementById('modalSerialBox').classList.remove('d-none');
            document.getElementById('modalScanArea').classList.add('d-none');
            document.getElementById('btnSimpanNfc').classList.remove('d-none');
            document.getElementById('btnScanNfc').innerHTML = '<i class="mdi mdi-refresh me-1"></i>Scan Ulang';
            document.getElementById('btnScanNfc').disabled = false;
        });
    } catch (err) {
        alert('Error: ' + err.message);
        document.getElementById('btnScanNfc').disabled = false;
        document.getElementById('btnScanNfc').innerHTML = '<i class="mdi mdi-nfc me-1"></i>Scan Kartu NFC';
    }
}

async function simpanNfc() {
    try {
        const res = await fetch('https://degenerative-unitalicized-dulcie.ngrok-free.dev/absensi/mahasiswa/nfc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'ngrok-skip-browser-warning': 'true',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                mahasiswa_id: selectedMhsId,
                nfc_serial: detectedSerial
            })
        });

        if (!res.ok) {
            const text = await res.text();
            alert('Gagal menyimpan: ' + text);
            return;
        }

        alert('NFC berhasil didaftarkan!');
        location.reload();

    } catch (e) {
        alert('ERROR => ' + e);
    }
}
</script>
@endsection