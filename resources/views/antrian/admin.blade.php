@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-view-dashboard"></i>
        </span>
        Admin – Antrian RS Digital
    </h3>
    <div class="d-flex gap-2">
        <a href="{{ route('antrian.papan') }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-monitor me-1"></i>Papan Antrian
        </a>
        <a href="{{ route('antrian.guest') }}" target="_blank" class="btn btn-outline-success btn-sm">
            <i class="mdi mdi-account-plus me-1"></i>Form Guest
        </a>
        <button id="btnReset" class="btn btn-outline-danger btn-sm">
            <i class="mdi mdi-refresh me-1"></i>Reset
        </button>
    </div>
</div>

{{-- Nomor sedang dipanggil --}}
<div class="row mb-4">
    <div class="col-12">
        <div id="cardDipanggil" class="card" style="border-left: 5px solid #f59e0b; display:none;">
            <div class="card-body d-flex align-items-center gap-4 py-3">
                <div style="font-size: 56px; font-weight: 900; color: #f59e0b; line-height: 1;" id="nomorDipanggilBesar">---</div>
                <div>
                    <div style="font-size: 11px; color: #888; letter-spacing: 1px; text-transform: uppercase;">Sedang Dipanggil</div>
                    <div style="font-size: 22px; font-weight: 700; color: #333;" id="namaDipanggilBesar">-</div>
                    <div style="font-size: 13px; color: #1a73e8;" id="poliDipanggilBesar"></div>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size:13px;">Dipanggil</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats cards --}}
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="card" style="border-top: 4px solid #1a73e8;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-timer-sand text-primary" style="font-size:28px;"></i>
                <div style="font-size: 36px; font-weight: 700; color: #1a73e8;" id="countMenunggu">0</div>
                <div style="font-size: 13px; color: #888;">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card" style="border-top: 4px solid #f59e0b;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-bullhorn text-warning" style="font-size:28px;"></i>
                <div style="font-size: 36px; font-weight: 700; color: #f59e0b;" id="countDipanggil">0</div>
                <div style="font-size: 13px; color: #888;">Dipanggil</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card" style="border-top: 4px solid #ef4444;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-clock-alert text-danger" style="font-size:28px;"></i>
                <div style="font-size: 36px; font-weight: 700; color: #ef4444;" id="countTerlambat">0</div>
                <div style="font-size: 13px; color: #888;">Terlambat</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card" style="border-top: 4px solid #16a34a;">
            <div class="card-body text-center py-3">
                <i class="mdi mdi-check-circle text-success" style="font-size:28px;"></i>
                <div style="font-size: 36px; font-weight: 700; color: #16a34a;" id="countSelesai">0</div>
                <div style="font-size: 13px; color: #888;">Selesai</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel antrian full width --}}
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        Daftar Antrian Hari Ini
                        <span id="totalAntrian" class="badge bg-primary ms-2">0</span> antrian
                    </h4>
                    <button id="btnPanggil" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-bullhorn me-1"></i>Panggil Berikutnya
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nama Pasien</th>
                                <th>Poli / Layanan</th>
                                <th>Jam Daftar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="6" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal panggil terlambat --}}
<div class="modal fade" id="modalTerlambat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Panggil Kembali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Panggil kembali: <strong id="modalNamaTerlambat"></strong></p>
                <p class="text-muted" style="font-size:13px;" id="modalPoliTerlambat"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="btnKonfirmasiTerlambat" class="btn btn-gradient-primary">
                    <i class="mdi mdi-bullhorn me-1"></i>Panggil
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let selectedTerlambatId = null;

// ================= SSE =================
function mulaiSSE() {
    const source = new EventSource('http://127.0.0.1:8002/api/stream');

    source.onopen = function () {
        console.log('SSE Connected');
    };

    source.addEventListener('queue-update', function (e) {
        try {
            const data = JSON.parse(e.data);

            console.log(data);

            renderStats(data.counts || {});
            renderDipanggil(data.dipanggil);

            // gabungkan semua data
            const all = [
                ...(data.dipanggil ? [data.dipanggil] : []),
                ...(data.menunggu || []),
                ...(data.terlambat || [])
            ];

            renderTabel(all);

            const total =
                (data.counts?.menunggu ?? 0) +
                (data.counts?.dipanggil ?? 0) +
                (data.counts?.terlambat ?? 0) +
                (data.counts?.selesai ?? 0);

            document.getElementById('totalAntrian').textContent = total;

        } catch(err) {
            console.error('Render Error:', err);
        }
    });

    source.onerror = function () {
        console.warn('SSE terputus, reconnect dalam 3 detik...');
        source.close();
        setTimeout(mulaiSSE, 3000);
    };
}

mulaiSSE();

// ================= STATS =================
function renderStats(counts) {
    document.getElementById('countMenunggu').textContent  = counts.menunggu ?? 0;
    document.getElementById('countDipanggil').textContent = counts.dipanggil ?? 0;
    document.getElementById('countTerlambat').textContent = counts.terlambat ?? 0;
    document.getElementById('countSelesai').textContent   = counts.selesai ?? 0;
}

// ================= DIPANGGIL =================
function renderDipanggil(item) {

    const card = document.getElementById('cardDipanggil');

    if (!item) {
        card.style.display = 'none';
        return;
    }

    card.style.display = 'block';

    document.getElementById('nomorDipanggilBesar').textContent =
        String(item.nomor_antrian).padStart(3,'0');

    document.getElementById('namaDipanggilBesar').textContent =
        item.nama;

    document.getElementById('poliDipanggilBesar').textContent =
        item.poli;
}

// ================= TABEL =================
function renderTabel(list) {

    const tbody = document.getElementById('tableBody');

    if (!list.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Belum ada antrian hari ini
                </td>
            </tr>
        `;
        return;
    }

    const badge = {
        menunggu  : '<span class="badge bg-primary">Menunggu</span>',
        dipanggil : '<span class="badge bg-warning text-dark">Dipanggil</span>',
        terlambat : '<span class="badge bg-danger">Terlambat</span>',
        selesai   : '<span class="badge bg-success">Selesai</span>',
    };

    tbody.innerHTML = list.map(a => {

        const jam = a.created_at
            ? a.created_at.substring(11,16)
            : '-';

        return `
            <tr>
                <td>
                    <strong>
                        ${String(a.nomor_antrian).padStart(3,'0')}
                    </strong>
                </td>

                <td>${a.nama}</td>

                <td>${a.poli}</td>

                <td>${jam}</td>

                <td>${badge[a.status] ?? '-'}</td>

                <td>

                    <button
                        class="btn btn-sm btn-warning me-1"
                        onclick="aksiPanggilById(${a.id}, '${a.nama}', '${a.poli}')"
                        ${a.status === 'terlambat' ? '' : 'disabled'}
                    >
                        <i class="mdi mdi-bullhorn"></i>
                    </button>

                    <button
                        class="btn btn-sm btn-success me-1"
                        onclick="aksiSelesai(${a.id})"
                        ${a.status === 'dipanggil' ? '' : 'disabled'}
                    >
                        <i class="mdi mdi-check"></i>
                    </button>

                    <button
                        class="btn btn-sm btn-danger"
                        onclick="aksiTerlambat(${a.id})"
                        ${['menunggu','dipanggil'].includes(a.status) ? '' : 'disabled'}
                    >
                        <i class="mdi mdi-clock-alert"></i>
                    </button>

                </td>
            </tr>
        `;

    }).join('');
}

// ================= ACTIONS =================

document.getElementById('btnPanggil')
.addEventListener('click', async function () {
    this.disabled = true;
    try {
        await post('{{ route("antrian.panggil") }}', {});
    } catch(err) {
        console.error(err);
        alert('Gagal memanggil antrian');
    } finally {
        this.disabled = false;
    }
});

async function aksiSelesai(id) {
    try {
        await post('{{ route("antrian.selesai") }}', {
            id
        });
    } catch(err) {
        console.error(err);
    }
}

async function aksiTerlambat(id) {
    if (!confirm('Tandai terlambat?')) return;
    try {
        await post('{{ route("antrian.terlambat") }}', {
            id
        });
    } catch(err) {
        console.error(err);
    }
}

function aksiPanggilById(id, nama, poli) {
    selectedTerlambatId = id;

    document.getElementById('modalNamaTerlambat').textContent = nama;
    document.getElementById('modalPoliTerlambat').textContent = poli;

    new bootstrap.Modal(
        document.getElementById('modalTerlambat')
    ).show();
}

document.getElementById('btnKonfirmasiTerlambat')
.addEventListener('click', async function () {
    try {
        await post('{{ route("antrian.panggil-terlambat") }}', {
            id: selectedTerlambatId
        });
    } catch(err) {
        console.error(err);
    } finally {
        bootstrap.Modal
            .getInstance(document.getElementById('modalTerlambat'))
            .hide();
    }
});

document.getElementById('btnReset')
.addEventListener('click', async function () {
    if (!confirm('Reset semua antrian hari ini?')) return;
    try {
        await post('{{ route("antrian.reset") }}', {});
    } catch(err) {
        console.error(err);
    }
});

// ================= FETCH HELPER =================
async function post(url, body) {

    const res = await fetch(url, {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },

        body: JSON.stringify(body)
    });

    const data = await res.json();

    if (!res.ok) {
        throw new Error(data.error || 'Request gagal');
    }

    return data;
}
</script>
@endsection