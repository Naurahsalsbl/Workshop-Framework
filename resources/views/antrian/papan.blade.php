<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian – RS Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0d1b3e;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: #0a1628;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #1a73e8;
        }
        .header-left h1 { font-size: 20px; font-weight: 700; color: #fff; }
        .header-left p  { font-size: 12px; color: #748ab0; margin-top: 2px; }
        .header-right   { display: flex; align-items: center; gap: 16px; }
        .clock { font-size: 28px; font-weight: 700; color: #1a73e8; letter-spacing: 2px; }
        .date  { font-size: 12px; color: #748ab0; text-align: right; }
        .live-badge {
            display: flex; align-items: center; gap: 6px;
            background: rgba(22, 163, 74, 0.15);
            border: 1px solid rgba(22,163,74,0.4);
            color: #16a34a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .live-dot {
            width: 7px; height: 7px; background: #16a34a; border-radius: 50%;
            animation: pulse 1.2s ease-in-out infinite;
        }

        /* Main layout */
        .main { display: flex; flex: 1; }

        /* Panel kiri: nomor dipanggil */
        .panel-kiri {
            flex: 1.3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: #0d1b3e;
            border-right: 1px solid #1a2d5a;
        }
        .label-dipanggil {
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #748ab0;
            margin-bottom: 20px;
        }
        .nomor-besar {
            font-size: 150px;
            font-weight: 900;
            color: #f59e0b;
            line-height: 1;
            text-shadow: 0 0 60px rgba(245, 158, 11, 0.4);
            transition: all 0.3s;
        }
        .nama-dipanggil {
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            margin-top: 12px;
            text-align: center;
        }
        .poli-dipanggil {
            font-size: 16px;
            color: #748ab0;
            margin-top: 6px;
            text-align: center;
        }
        .btn-poli {
            margin-top: 20px;
            background: #f59e0b;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 24px;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-poli span { font-size: 18px; }

        /* Panel kanan: antrian menunggu */
        .panel-kanan {
            flex: 0.9;
            background: #0a1628;
            padding: 1.5rem;
            overflow-y: auto;
            max-height: calc(100vh - 70px);
        }
        .panel-kanan h3 {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #748ab0;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #1a2d5a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .antrian-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 8px;
            background: rgba(255,255,255,0.04);
        }
        .antrian-item:first-child {
            background: rgba(26, 115, 232, 0.15);
            border: 1px solid rgba(26, 115, 232, 0.3);
        }
        .antrian-nomor { font-size: 22px; font-weight: 800; color: #1a73e8; min-width: 52px; }
        .antrian-info  {}
        .antrian-nama  { font-size: 14px; font-weight: 600; color: #e2e8f0; }
        .antrian-poli  { font-size: 12px; color: #748ab0; margin-top: 2px; }

        /* Flash animasi */
        @keyframes flash { 0%, 100% { background: #0d1b3e; } 50% { background: #1a2d6e; } }
        .panel-kiri.flash { animation: flash 0.5s ease 3; }

        /* Overlay aktivasi */
        .overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.9);
            display: flex; align-items: center; justify-content: center;
            z-index: 999;
        }
        .overlay-box {
            background: #0a1628;
            border: 2px solid #1a73e8;
            border-radius: 16px;
            padding: 2.5rem 3rem;
            text-align: center;
            max-width: 400px;
        }
        .overlay-box h2 { font-size: 22px; margin-bottom: 8px; }
        .overlay-box p  { color: #748ab0; font-size: 14px; margin-bottom: 20px; }
        .btn-aktivasi {
            background: #1a73e8; color: #fff; border: none;
            padding: 12px 32px; border-radius: 10px;
            font-size: 16px; font-weight: 600; cursor: pointer;
        }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    </style>
</head>
<body>

<div class="overlay" id="overlay">
    <div class="overlay-box">
        <h2>📺 Papan Antrian</h2>
        <p>Klik tombol di bawah untuk mengaktifkan tampilan dan notifikasi suara otomatis.</p>
        <button class="btn-aktivasi" onclick="aktivasi()">Aktifkan Papan Antrian</button>
    </div>
</div>

<div class="header">
    <div class="header-left">
        <h1>RS Digital</h1>
        <p>Sistem Antrian Digital</p>
    </div>
    <div class="header-right">
        <div>
            <div class="clock" id="clock">--:--:--</div>
            <div class="date" id="dateStr"></div>
        </div>
        <div class="live-badge"><div class="live-dot"></div> Live</div>
    </div>
</div>

<div class="main">
    {{-- Panel kiri --}}
    <div class="panel-kiri" id="panelKiri">
        <div class="label-dipanggil">Nomor Dipanggil</div>
        <div class="nomor-besar" id="nomorBesar">---</div>
        <div class="nama-dipanggil" id="namaBesar">Menunggu panggilan...</div>
        <div class="poli-dipanggil" id="poliBesar"></div>
        <button class="btn-poli" id="btnPoliLabel" style="display:none;">
            <span>🏥</span> <span id="btnPoliTeks">Silakan Menuju Poli</span>
        </button>
    </div>

    {{-- Panel kanan --}}
    <div class="panel-kanan">
        <h3>
            <span>⏳</span> Antrian Menunggu
        </h3>
        <div id="listMenunggu">
            <p style="color:#4a5568; text-align:center; font-size:13px;">Belum ada antrian</p>
        </div>
    </div>
</div>

<script>
let sudahAktif    = false;

// ── Simpan ID terakhir yang dipanggil untuk deteksi panggilan baru ──
let lastDipanggilId = null;

// ─── Jam & tanggal ────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
    document.getElementById('dateStr').textContent = now.toLocaleDateString('id-ID', {
        weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
    });
}
setInterval(updateClock, 1000);
updateClock();

// ─── Aktivasi ─────────────────────────────────────────────────────
function aktivasi() {
    sudahAktif = true;
    document.getElementById('overlay').style.display = 'none';
    mulaiSSE();
}

// ─── SSE ──────────────────────────────────────────────────────────
function mulaiSSE() {
    const source = new EventSource('http://127.0.0.1:8002/api/stream');

    // HAPUS: source.onmessage — tidak diperlukan, sudah ada addEventListener di bawah

    source.addEventListener('queue-update', function(e) {
        try {
            const data = JSON.parse(e.data);

            renderMenunggu(data.menunggu || []);

            const dipanggil = data.dipanggil;

            if (dipanggil) {
                tampilkanDipanggil(dipanggil);
            } else {
                // Reset tampilan jika tidak ada yang dipanggil
                lastDipanggilId = null;
                document.getElementById('nomorBesar').textContent    = '---';
                document.getElementById('namaBesar').textContent     = 'Menunggu panggilan...';
                document.getElementById('poliBesar').textContent     = '';
                document.getElementById('btnPoliLabel').style.display = 'none';
            }
        } catch(err) {
            console.error('Render error:', err);
        }
    });

    // FIX: reconnect manual jika SSE error/putus
    source.onerror = function() {
        console.warn('SSE terputus, mencoba reconnect...');
        source.close();
        setTimeout(mulaiSSE, 3000);
    };
}

// ─── Tampilkan dipanggil ──────────────────────────────────────────
function tampilkanDipanggil(item) {
    // FIX: Cek apakah ini panggilan BARU (berbeda dari sebelumnya)
    // Gunakan gabungan id + nomor_antrian sebagai pengenal unik
    const idSekarang = (item.id ?? '') + '_' + (item.nomor_antrian ?? '');

    const adalahPanggilanBaru = (idSekarang !== lastDipanggilId);

    // Update tampilan selalu (agar nomor tampil benar setelah reload)
    document.getElementById('nomorBesar').textContent = String(item.nomor_antrian).padStart(3, '0');
    document.getElementById('namaBesar').textContent  = item.nama;
    document.getElementById('poliBesar').textContent  = item.poli;

    const btnPoli = document.getElementById('btnPoliLabel');
    document.getElementById('btnPoliTeks').textContent = 'Silakan Menuju ' + item.poli;
    btnPoli.style.display = 'flex';

    // FIX: Flash animasi & suara HANYA saat panggilan baru
    if (adalahPanggilanBaru) {
        lastDipanggilId = idSekarang;

        const panel = document.getElementById('panelKiri');
        panel.classList.add('flash');
        setTimeout(() => panel.classList.remove('flash'), 1800);

        // FIX: Panggil suara — fungsi sudah ada, tinggal dipanggil di sini
        bunyikanPanggilan(item);
    }
}

// ─── Render daftar menunggu ───────────────────────────────────────
function renderMenunggu(list) {
    const el = document.getElementById('listMenunggu');
    if (!list.length) {
        el.innerHTML = '<p style="color:#4a5568; text-align:center; font-size:13px;">Belum ada antrian</p>';
        return;
    }
    el.innerHTML = list.map(a => `
        <div class="antrian-item">
            <div class="antrian-nomor">${String(a.nomor_antrian).padStart(3, '0')}</div>
            <div class="antrian-info">
                <div class="antrian-nama">${a.nama}</div>
                <div class="antrian-poli">${a.poli}</div>
            </div>
        </div>`).join('');
}

// ─── Suara panggilan (Web Speech API) ────────────────────────────
function bunyikanPanggilan(item) {
    if (!('speechSynthesis' in window)) {
        console.warn('Browser tidak mendukung Web Speech API');
        return;
    }

    // Batalkan speech yang sedang berjalan
    window.speechSynthesis.cancel();

    const teks = `Nomor antrian ${item.nomor_antrian}. ${item.nama}. Silakan menuju ${item.poli}.`;
    const ucap = new SpeechSynthesisUtterance(teks);
    ucap.lang   = 'id-ID';
    ucap.rate   = 0.85;
    ucap.pitch  = 1.0;
    ucap.volume = 1.0;
    window.speechSynthesis.speak(ucap);
}
</script>
</body>
</html>