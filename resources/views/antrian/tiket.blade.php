<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian #{{ str_pad($nomor, 3, '0', STR_PAD_LEFT) }} – RS Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .brand { text-align: center; color: #fff; margin-bottom: 1.5rem; }
        .brand h1 { font-size: 28px; font-weight: 700; }
        .brand p  { font-size: 13px; opacity: 0.8; }

        .ticket {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 380px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .ticket-header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            padding: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .ticket-header p { font-size: 12px; opacity: 0.8; letter-spacing: 1px; text-transform: uppercase; }
        .nomor { font-size: 88px; font-weight: 900; line-height: 1; margin: 8px 0; }
        .nama  { font-size: 20px; font-weight: 600; }

        .ticket-body { padding: 1.5rem; }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #888; }
        .info-value { font-weight: 600; color: #333; }

        .status-box {
            margin: 1rem 1.5rem 1.5rem;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .status-menunggu  { background: #fff8e1; color: #f59e0b; border: 1px solid #fde68a; }
        .status-dipanggil { background: #e8f5e9; color: #16a34a; border: 1px solid #bbf7d0; font-size: 15px; }
        .status-terlambat { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .dot {
            width: 8px; height: 8px; border-radius: 50%;
            animation: pulse 1.2s ease-in-out infinite;
        }
        .dot-yellow { background: #f59e0b; }
        .dot-green  { background: #16a34a; }
        .dot-red    { background: #dc2626; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    </style>
</head>
<body>
<div class="brand">
    <h1>RS Digital</h1>
    <p>Sistem Antrian Digital</p>
</div>

<div class="ticket">
    <div class="ticket-header">
        <p>Nomor Antrian Anda</p>
        <div class="nomor">{{ str_pad($nomor, 3, '0', STR_PAD_LEFT) }}</div>
        <div class="nama">{{ $nama }}</div>
    </div>

    <div class="ticket-body">
        <div class="info-row">
            <span class="info-label">Poli / Layanan</span>
            <span class="info-value">{{ $poli }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jam Daftar</span>
            <span class="info-value">{{ now()->format('H:i') }}</span>
        </div>
    </div>

    <div id="statusBox" class="status-box status-menunggu">
        <span class="dot dot-yellow"></span>
        Menunggu dipanggil...
    </div>
</div>

<script>
const nomorSaya = {{ $nomor }};
const source = new EventSource('/api/stream');

source.addEventListener('queue-update', function (e) {

    const data = JSON.parse(e.data);
    const box  = document.getElementById('statusBox');

    // STATUS DIPANGGIL
    if (data.dipanggil && data.dipanggil.nomor == nomorSaya) {

        box.className = 'status-box status-dipanggil';

        box.innerHTML = `
            <span class="dot dot-green"></span>
            🎉 Nomor Anda dipanggil!
            Silakan menuju <strong>${data.dipanggil.poli}</strong>
        `;

        return;
    }

    // STATUS TERLAMBAT
    const terlambat = (data.terlambat || []).find(
        t => t.nomor == nomorSaya
    );

    if (terlambat) {

        box.className = 'status-box status-terlambat';

        box.innerHTML = `
            <span class="dot dot-red"></span>
            Nomor Anda ditandai tidak hadir.
            Harap melapor ke petugas.
        `;

        return;
    }

    // STATUS MENUNGGU
    box.className = 'status-box status-menunggu';

    box.innerHTML = `
        <span class="dot dot-yellow"></span>
        Menunggu dipanggil...
    `;
});
</script>
</body>
</html>