<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Antrian – RS Digital</title>
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
        .brand { text-align: center; color: #fff; margin-bottom: 2rem; }
        .brand h1 { font-size: 36px; font-weight: 700; }
        .brand p  { font-size: 15px; opacity: 0.85; margin-top: 4px; }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .card h2 { font-size: 20px; font-weight: 700; text-align: center; color: #1a1a2e; margin-bottom: 4px; }
        .card p  { font-size: 13px; color: #888; text-align: center; margin-bottom: 1.5rem; }

        .form-group { margin-bottom: 1.2rem; }
        label { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
        input, select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            color: #333;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus, select:focus { border-color: #1a73e8; }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }

        .error { color: #e53935; font-size: 12px; margin-top: 4px; }

        .loading { text-align: center; padding: 2rem 0; }
        .spinner {
            width: 40px; height: 40px;
            border: 4px solid #e0e0e0;
            border-top-color: #1a73e8;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="brand">
    <h1>RS Digital</h1>
    <p>Sistem Antrian Digital</p>
</div>

<div class="card">
    <h2>Ambil Nomor Antrian</h2>
    <p>Isi data diri Anda untuk mendapatkan nomor antrian</p>

    <div id="formSection">
        <div class="form-group">
            <label>👤 Nama Lengkap</label>
            <input type="text" id="inputNama" placeholder="Masukkan nama lengkap Anda">
            <div class="error d-none" id="errNama"></div>
        </div>

        <div class="form-group">
            <label>🏥 Pilih Poli</label>
            <select id="selectPoli">
                <option value="">-- Pilih Poli --</option>
                @foreach($poliList as $poli)
                    <option value="{{ $poli }}">{{ $poli }}</option>
                @endforeach
            </select>
            <div class="error" id="errPoli" style="display:none;"></div>
        </div>

        <button class="btn" id="btnDaftar">🎫 Ambil Nomor Antrian</button>
    </div>

    <div id="loadingSection" style="display:none;" class="loading">
        <div class="spinner"></div>
        <p style="color:#888;">Memproses pendaftaran...</p>
    </div>
</div>

<script>
document.getElementById('btnDaftar').addEventListener('click', async function () {
    const nama = document.getElementById('inputNama').value.trim();
    const poli = document.getElementById('selectPoli').value;
    let valid  = true;

    document.getElementById('errNama').style.display = 'none';
    document.getElementById('errPoli').style.display = 'none';

    if (!nama) {
        document.getElementById('errNama').textContent = 'Nama tidak boleh kosong!';
        document.getElementById('errNama').style.display = 'block';
        valid = false;
    }
    if (!poli) {
        document.getElementById('errPoli').textContent = 'Pilih poli terlebih dahulu!';
        document.getElementById('errPoli').style.display = 'block';
        valid = false;
    }
    if (!valid) return;

    document.getElementById('formSection').style.display = 'none';
    document.getElementById('loadingSection').style.display = 'block';

    try {
        const res  = await fetch('{{ route("antrian.daftar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nama, poli })
        });
        const data = await res.json();

        // Buka tiket di tab baru
        const url = `{{ route("antrian.tiket") }}?nomor=${data.nomor}&nama=${encodeURIComponent(data.nama)}&poli=${encodeURIComponent(data.poli)}`;
        window.open(url, '_blank');

        // Reset form
        document.getElementById('inputNama').value = '';
        document.getElementById('selectPoli').value = '';
        document.getElementById('formSection').style.display = 'block';
        document.getElementById('loadingSection').style.display = 'none';
    } catch (e) {
        alert('Gagal mendaftar, coba lagi.');
        document.getElementById('formSection').style.display = 'block';
        document.getElementById('loadingSection').style.display = 'none';
    }
});

document.getElementById('inputNama').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') document.getElementById('btnDaftar').click();
});
</script>
</body>
</html>