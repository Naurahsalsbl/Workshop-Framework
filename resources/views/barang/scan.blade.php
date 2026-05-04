@extends('layout.main')

@section('content')

<div class="container">

    <h3>Scan Barcode</h3>

    <div id="reader" style="width:300px;"></div>

    <div class="mt-3">
        <h5>Hasil Scan:</h5>
        <div id="hasil" class="alert alert-info"></div>
    </div>

    <audio id="beep" src="{{ asset('audio/beep.mp3') }}"></audio>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>


<script>

function onScanSuccess(decodedText) {

    // beep
    beep.currentTime = 0;
    beep.play().catch(() => {});

    // stop scanner
    html5QrcodeScanner.clear();

    console.log("SCAN:", decodedText);

    fetch('http://127.0.0.1:8000/api/barang/' + decodedText.trim())
        .then(res => res.json())
        .then(data => {

            if(data){
                document.getElementById("hasil").innerHTML =
                    "<b>ID Barang:</b> " + data.id_barang + "<br>" +
                    "<b>Nama:</b> " + data.nama + "<br>" +
                    "<b>Harga:</b> Rp " + parseInt(data.harga).toLocaleString('id-ID');
            } else {
                document.getElementById("hasil").innerHTML =
                    "❌ Barang tidak ditemukan";
            }
        })
        .catch(err => {
            document.getElementById("hasil").innerHTML =
                "❌ Error: " + err.message;
        });
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: 250 }
);

html5QrcodeScanner.render(onScanSuccess);
</script>

@endsection