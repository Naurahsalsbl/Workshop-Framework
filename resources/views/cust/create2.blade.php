@extends('layout.main')

@section('content')
<div class="container mt-4">
    <h3>Tambah Customer (File)</h3>

    <form action="/customer/store2" method="POST">
        @csrf

        <input type="text" name="nama" placeholder="Nama" class="form-control mb-2">
        <input type="text" name="alamat" placeholder="Alamat" class="form-control mb-2">
        <input type="text" name="provinsi" placeholder="Provinsi" class="form-control mb-2">
        <input type="text" name="kota" placeholder="Kota" class="form-control mb-2">
        <input type="text" name="kecamatan" placeholder="Kecamatan" class="form-control mb-2">
        <input type="text" name="kodepos" placeholder="Kode Pos" class="form-control mb-2">

        <video id="video" width="250" autoplay></video>
        <br>
        <button type="button" onclick="capture()">Ambil Foto</button>

        <canvas id="canvas" width="250" height="200"></canvas>

        <input type="hidden" name="foto" id="foto">

        <br><br>
        <button type="submit">Simpan</button>
    </form>
</div>

<script>
navigator.mediaDevices.getUserMedia({ video: true })
.then(stream => {
    document.getElementById('video').srcObject = stream;
});

function capture() {
    let canvas = document.getElementById('canvas');
    let video = document.getElementById('video');

    canvas.getContext('2d').drawImage(video, 0, 0, 250, 200);

    let data = canvas.toDataURL('image/png');
    document.getElementById('foto').value = data;
}
</script>

@endsection