<!DOCTYPE html>
<html>
<head>
    <title>Scan QR Vendor</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

<h2>Scan QR Code (Vendor)</h2>

<div id="reader" style="width:300px;"></div>

<p id="hasil"></p>

<audio id="beep" src="https://cdn.pixabay.com/audio/2022/03/15/audio_115b9b0f05.mp3"></audio>

<script>

// ambil data pesanan dari localStorage
let database = {
    pesanan: {}
};

let saved = localStorage.getItem("pesanan");
if(saved){
    database.pesanan = JSON.parse(saved);
}

function onScanSuccess(decodedText) {

    // 🔊 bunyi beep
    document.getElementById("beep").play();

    // ⛔ stop scanner
    html5QrcodeScanner.clear();

    // ambil data pesanan
    let pesanan = database.pesanan[decodedText];

    if(pesanan){
        document.getElementById("hasil").innerHTML =
            "<b>ID Pesanan:</b> " + decodedText + "<br>" +
            "<b>Menu:</b> " + pesanan.menu.join(", ") + "<br>" +
            "<b>Total:</b> Rp" + pesanan.total + "<br>" +
            "<b>Status:</b> " + pesanan.status;
    } else {
        document.getElementById("hasil").innerHTML =
            "❌ Pesanan tidak ditemukan";
    }
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", 
    { fps: 10, qrbox: 250 }
);

html5QrcodeScanner.render(onScanSuccess);

</script>

</body>
</html>