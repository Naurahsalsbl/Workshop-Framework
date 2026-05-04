@extends('layout.main')

@section('content')

<div class="container">
    <h3>Scan QR Customer</h3>

    <!-- tombol wajib biar audio aktif -->
    <button onclick="startScanner()" class="btn btn-primary mb-3">
        Aktifkan Scanner
    </button>

    <!-- kamera -->
    <div id="reader" style="width:300px;"></div>

    <!-- hasil -->
    <div class="mt-3">
        <h5>Hasil Scan:</h5>
        <div id="hasil" class="alert alert-info"></div>
    </div>

    <!-- AUDIO LOKAL -->
    <audio id="beep" src="{{ asset('audio/beep.mp3') }}"></audio>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let html5QrcodeScanner;
let beep = document.getElementById("beep");

// START SCANNER
function startScanner() {

    // unlock audio (biar boleh bunyi)
    beep.play().then(() => {
        beep.pause();
        beep.currentTime = 0;
    }).catch(() => {});

    // reset biar ga dobel
    document.getElementById("reader").innerHTML = "";

    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: 250 }
    );

    html5QrcodeScanner.render(onScanSuccess);
}

// ✅ SUCCESS SCAN
function onScanSuccess(decodedText) {

    // BEEP
    beep.currentTime = 0;
    beep.play().catch(() => {});

    // stop scanner
    html5QrcodeScanner.clear();

    // ambil ID dari QR (format URL)
    let parts = decodedText.split('/');
    let id = parts[parts.length - 1];

    // ambil data dari API
    fetch("{{ url('/api/pesanan') }}/" + id)
        .then(res => {
            if (!res.ok) throw new Error("Data tidak ditemukan");
            return res.json();
        })
        .then(data => {

            let html = `
                <b>ID Pesanan:</b> ${data.pesanan.idpesanan}<br>
                <b>Nama:</b> ${data.pesanan.nama}<br>
                <b>Status:</b> ${
                    data.pesanan.status_bayar == 1 
                    ? '<span style="color:green">Lunas</span>' 
                    : '<span style="color:red">Belum Bayar</span>'
                }<br><br>

                <b>Menu:</b><br>
            `;

            data.menu.forEach(item => {
                html += `- ${item.nama} (${item.qty}x)<br>`;
            });

            document.getElementById("hasil").innerHTML = html;
        })
        .catch(err => {
            document.getElementById("hasil").innerHTML = "❌ " + err.message;
        });
}
</script>

@endsection