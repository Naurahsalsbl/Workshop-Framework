@extends('layout.main')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">
                    Scan Kunjungan Toko
                </h4>
                <div id="reader"></div>
                <div id="hasil" class="mt-3 d-none"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>

// ─────────────────────────────────────────────
// Ambil lokasi GPS akurat
// ─────────────────────────────────────────────
function getAccuratePosition(
    targetAccuracy = 50,
    maxWait = 20000
) {

    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();
        const watchId =
            navigator.geolocation.watchPosition(
            (position) => {
                const acc =
                    position.coords.accuracy;
                if (
                    !bestResult ||
                    acc < bestResult.coords.accuracy
                ) {

                    bestResult = position;
                }
                // kalau akurasi sudah bagus
                if (acc <= targetAccuracy) {
                    navigator.geolocation
                        .clearWatch(watchId);
                    resolve(bestResult);
                }

                // timeout
                if (
                    Date.now() - startTime >= maxWait
                ) {
                    navigator.geolocation
                        .clearWatch(watchId);
                    if (bestResult) {
                        resolve(bestResult);
                    } else {
                        reject(
                            new Error('Timeout GPS')
                        );
                    }
                }
            },

            (error) => reject(error),

            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: maxWait
            }
        );
    });
}

// ─────────────────────────────────────────────
// Scanner
// ─────────────────────────────────────────────
const scanner =
    new Html5QrcodeScanner(

    "reader",

    {
        fps: 10,
        qrbox: 250,
        rememberLastUsedCamera: true,
        formatsToSupport: [

            Html5QrcodeSupportedFormats.CODE_39,

            Html5QrcodeSupportedFormats.CODE_128
        ]
    }
);

let processing = false;

// ─────────────────────────────────────────────
// Saat barcode berhasil discan
// ─────────────────────────────────────────────

async function onScanSuccess(decodedText) {

    console.log(decodedText);
    if (processing) return;

    processing = true;

    try {

        // ambil data toko
        const barcode =
        decodedText
        .replace(/\*/g, '')
        .trim()
        .toUpperCase();

        const tokoRes =
            await fetch(
                `/barcode/${barcode}`
            );

        if (!tokoRes.ok) {
            throw new Error(
                'Barcode tidak ditemukan'
            );
        }
        const toko =
            await tokoRes.json();

        // stop scanner
        await scanner.clear();

        // ambil GPS
        const pos =
            await getAccuratePosition(50);

        // validasi kunjungan
        const validasi =
            await fetch('/validasi', {
                method: 'POST',
                headers: {
                    'Content-Type':
                        'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                },

                body: JSON.stringify({
                    toko_id: toko.id,
                    lat_sales:
                        pos.coords.latitude,
                    lng_sales:
                        pos.coords.longitude,
                    accuracy_sales:
                        pos.coords.accuracy
                })
            });

        if (!validasi.ok) {
            throw new Error(
                'Validasi gagal'
            );
        }

        const result =
            await validasi.json();

        const diterima =
            result.status === 'DITERIMA';

        // tampil hasil
        document.getElementById('hasil')
            .innerHTML = `

            <div class="alert ${
                diterima
                ? 'alert-success'
                : 'alert-danger'
            }">

                <h5 class="mb-3">
                    ${
                        diterima
                        ? 'DITERIMA ✓'
                        : 'DITOLAK ✗'
                    }
                </h5>
                <div class="mb-2">
                    <strong>Toko:</strong><br>
                    ${result.nama_toko}
                </div>

                <div class="mb-2">
                    <strong>Jarak:</strong><br>
                    ${result.jarak_meter} meter
                </div>

                <div>
                    <strong>Threshold:</strong><br>
                    ${result.threshold_efektif} meter
                </div>

            </div>

            <button
                onclick="location.reload()"
                class="btn btn-primary w-100">

                Scan Lagi

            </button>
        `;

        document.getElementById('hasil')
            .classList.remove('d-none');

    } catch (e) {

        alert(e.message);
        processing = false;

        // restart scanner kalau gagal
        scanner.render(onScanSuccess);
    }
}

// ─────────────────────────────────────────────
// Jalankan scanner
// ─────────────────────────────────────────────
scanner.render(onScanSuccess);

</script>

@endsection