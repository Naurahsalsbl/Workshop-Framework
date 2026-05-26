@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-nfc"></i>
        </span>
        Absensi NFC
    </h3>
    <div class="d-flex gap-2">
        <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-table me-1"></i>Rekap
        </a>
        <a href="{{ route('absensi.mahasiswa') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-account-group me-1"></i>Mahasiswa
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">

        {{-- Pilih Matakuliah --}}
        <div class="card grid-margin">
            <div class="card-body">
                <h4 class="card-title mb-3">
                    <i class="mdi mdi-book-open text-primary me-2"></i>Pilih Matakuliah
                </h4>
                <select id="selectMatkul" class="form-control">
                    <option value="">-- Pilih Matakuliah --</option>
                    @foreach($matakuliah as $mk)
                        <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Scanner NFC --}}
        <div class="card grid-margin">
            <div class="card-body text-center">
                <h4 class="card-title mb-1">
                    <i class="mdi mdi-nfc-tap text-primary me-2"></i>Scanner NFC
                </h4>
                <p class="text-muted mb-4" style="font-size:13px;">
                    Gunakan smartphone Android Chrome untuk scan kartu NFC mahasiswa.
                </p>

                {{-- Area scan --}}
                <div id="areaScan" style="
                    width: 180px; height: 180px;
                    border-radius: 50%;
                    background: #f0f4ff;
                    border: 3px dashed #c5cae9;
                    display: flex; flex-direction: column;
                    align-items: center; justify-content: center;
                    margin: 0 auto 1.5rem;
                    transition: all 0.3s;
                ">
                    <i class="mdi mdi-nfc" style="font-size: 64px; color: #c5cae9;"></i>
                    <small style="color: #c5cae9; margin-top: 8px;">Belum aktif</small>
                </div>

                <p id="statusNfc" class="text-muted mb-3">Klik tombol untuk mengaktifkan NFC.</p>

                <button id="btnAktifkan" class="btn btn-gradient-primary w-100 mb-2" onclick="startScan()">
                    <i class="mdi mdi-nfc me-1"></i>Aktifkan NFC Scanner
                </button>
                <button id="btnStop" class="btn btn-outline-danger w-100 d-none" onclick="stopScan()">
                    <i class="mdi mdi-stop me-1"></i>Stop Scanner
                </button>

                {{-- Hasil scan --}}
                <div id="hasilScan" class="d-none mt-4">
                    <div id="hasilBox" class="rounded p-3"></div>
                </div>
            </div>
        </div>

        {{-- Log absensi hari ini --}}
        <div class="card grid-margin">
            <div class="card-body">
                <h4 class="card-title mb-3">
                    <i class="mdi mdi-history text-primary me-2"></i>Log Hari Ini
                </h4>
                <div id="logAbsensi">
                    <p class="text-muted text-center" style="font-size:13px;">Belum ada absensi.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let ndefReader  = null;
let logList     = [];

// ─── Cek dukungan NFC ─────────────────────────────────────────────
if (!('NDEFReader' in window)) {
    document.getElementById('statusNfc').textContent =
        '⚠️ Browser ini tidak mendukung Web NFC. Gunakan Android Chrome.';
    document.getElementById('btnAktifkan').disabled = true;
}

// ─── Start scan ───────────────────────────────────────────────────
async function startScan() {
    const matkulId = document.getElementById('selectMatkul').value;
    if (!matkulId) {
        alert('Pilih matakuliah terlebih dahulu!');
        return;
    }

    try {
        ndefReader = new NDEFReader();
        await ndefReader.scan();

        // Update UI
        setStatus('scanning');
        document.getElementById('btnAktifkan').classList.add('d-none');
        document.getElementById('btnStop').classList.remove('d-none');

        ndefReader.addEventListener('reading', ({ serialNumber, message }) => {
            prosesNfc(serialNumber, matkulId);
        });

        ndefReader.addEventListener('readingerror', () => {
            setStatus('error');
            document.getElementById('statusNfc').textContent = 'Gagal membaca kartu. Coba lagi.';
        });

    } catch (err) {
        document.getElementById('statusNfc').textContent = 'Error: ' + err.message;
        setStatus('idle');
    }
}

// ─── Stop scan ────────────────────────────────────────────────────
function stopScan() {
    ndefReader = null;
    setStatus('idle');
    document.getElementById('btnAktifkan').classList.remove('d-none');
    document.getElementById('btnStop').classList.add('d-none');
    document.getElementById('statusNfc').textContent = 'Scanner dihentikan.';
}

// ─── Proses serial NFC → kirim ke Laravel ─────────────────────────
async function prosesNfc(serialNumber, matkulId) {
    setStatus('processing');
    document.getElementById('statusNfc').textContent = 'Memproses kartu...';

    try {
        const res  = await fetch('https://degenerative-unitalicized-dulcie.ngrok-free.dev/absensi/proses-scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'ngrok-skip-browser-warning': 'true',
            },
            body: JSON.stringify({
                nfc_serial:    serialNumber,
                matakuliah_id: matkulId,
            }),
        });

        //alert('STATUS: ' + res.status);

        const data = await res.json();
        tampilkanHasil(data, serialNumber);

    } catch (err) {
        document.getElementById('statusNfc').textContent = 'Gagal menghubungi server.';
        setStatus('scanning');
    }
}

// ─── Tampilkan hasil ──────────────────────────────────────────────
function tampilkanHasil(data, serial) {
    const box = document.getElementById('hasilBox');
    document.getElementById('hasilScan').classList.remove('d-none');

    if (data.success) {
        const statusBadge = data.status === 'hadir'
            ? '<span class="badge bg-success">Hadir</span>'
            : '<span class="badge bg-warning text-dark">Terlambat</span>';

        box.className = 'rounded p-3 bg-success bg-opacity-10 border border-success';
        box.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="mdi mdi-check-circle text-success" style="font-size:48px;"></i>
                <div class="text-start">
                    <div class="fw-bold" style="font-size:18px;">${data.mahasiswa.nama}</div>
                    <div class="text-muted" style="font-size:13px;">NIM: ${data.mahasiswa.nim}</div>
                    <div class="mt-1">${statusBadge} <small class="text-muted ms-1">${data.waktu}</small></div>
                </div>
            </div>`;

        // Tambah ke log
        logList.unshift({ nama: data.mahasiswa.nama, nim: data.mahasiswa.nim, status: data.status, waktu: data.waktu });
        renderLog();

        setStatus('success');
        setTimeout(() => setStatus('scanning'), 3000);

    } else {
        box.className = 'rounded p-3 bg-danger bg-opacity-10 border border-danger';
        box.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="mdi mdi-close-circle text-danger" style="font-size:48px;"></i>
                <div class="text-start">
                    <div class="fw-bold text-danger">${data.message}</div>
                    <div class="text-muted" style="font-size:12px;">Serial: ${serial}</div>
                </div>
            </div>`;

        setStatus('error');
        setTimeout(() => setStatus('scanning'), 3000);
    }

    document.getElementById('statusNfc').textContent = data.success
        ? '✅ Absensi berhasil! Dekatkan kartu berikutnya.'
        : '❌ ' + data.message;
}

// ─── Render log ───────────────────────────────────────────────────
function renderLog() {
    const el = document.getElementById('logAbsensi');
    if (!logList.length) {
        el.innerHTML = '<p class="text-muted text-center" style="font-size:13px;">Belum ada absensi.</p>';
        return;
    }
    el.innerHTML = logList.map(l => `
        <div class="d-flex justify-content-between align-items-center p-2 mb-2 rounded bg-light">
            <div>
                <span class="fw-bold">${l.nama}</span>
                <small class="text-muted ms-1">${l.nim}</small>
            </div>
            <div>
                ${l.status === 'hadir'
                    ? '<span class="badge bg-success">Hadir</span>'
                    : '<span class="badge bg-warning text-dark">Terlambat</span>'}
                <small class="text-muted ms-1">${l.waktu}</small>
            </div>
        </div>`).join('');
}

// ─── Set status visual area scan ──────────────────────────────────
function setStatus(state) {
    const area = document.getElementById('areaScan');
    const configs = {
        idle: {
            border: '#c5cae9', bg: '#f0f4ff', iconColor: '#c5cae9',
            label: 'Belum aktif'
        },
        scanning: {
            border: '#1a73e8', bg: '#e8f0fe', iconColor: '#1a73e8',
            label: 'Siap scan...'
        },
        processing: {
            border: '#f59e0b', bg: '#fff8e1', iconColor: '#f59e0b',
            label: 'Memproses...'
        },
        success: {
            border: '#16a34a', bg: '#e8f5e9', iconColor: '#16a34a',
            label: 'Berhasil!'
        },
        error: {
            border: '#dc2626', bg: '#fef2f2', iconColor: '#dc2626',
            label: 'Gagal'
        },
    };

    const cfg = configs[state] || configs.idle;
    area.style.borderColor     = cfg.border;
    area.style.backgroundColor = cfg.bg;
    area.querySelector('i').style.color          = cfg.iconColor;
    area.querySelector('small').textContent      = cfg.label;
    area.querySelector('small').style.color      = cfg.iconColor;
}
</script>
@endsection