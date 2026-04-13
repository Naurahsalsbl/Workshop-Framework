<!DOCTYPE html>
<html>
<head>
    <title>Scan QR</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body>

<h2>Scan QR Code</h2>

<div id="reader" style="width:300px;"></div>

<script>
function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code scanned = ${decodedText}`);

    // redirect ke URL dari QR
    window.location.href = decodedText;
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", 
    { fps: 10, qrbox: 250 }
);

html5QrcodeScanner.render(onScanSuccess);
</script>

</body>
</html>