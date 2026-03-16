<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sales - Kasir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .card-header { border-radius: 12px 12px 0 0 !important; font-weight: 600; }
        .input-readonly { background-color: #fff3cd !important; cursor: not-allowed; }
        #tabel-keranjang th { background-color: #343a40; color: white; }
        .btn-hapus { padding: 2px 8px; font-size: 0.8rem; }
        #total-row td { font-weight: bold; font-size: 1.05rem; background: #e8f5e9; }
        .version-tab .nav-link { font-weight: 600; }
        #notif-barang { font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="container py-5">

    <h3 class="mb-4 fw-bold">Point Of Sales</h3>

    <!-- ================= FORM ================= -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-2">Kode barang :</div>
        <div class="col-md-8">
            <input type="text" id="input-kode" class="form-control">
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-2">Nama barang :</div>
        <div class="col-md-8">
            <input type="text" id="input-nama"
                   class="form-control input-readonly" readonly>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-2">Harga barang :</div>
        <div class="col-md-8">
            <input type="text" id="input-harga"
                   class="form-control input-readonly" readonly>
        </div>
    </div>

    <div class="row mb-4 align-items-center">
        <div class="col-md-2">Jumlah :</div>
        <div class="col-md-6">
            <input type="number" id="input-jumlah"
                   class="form-control" value="1" min="1">
        </div>

        <div class="col-md-2">
            <button id="btn-tambah"
                    class="btn btn-success w-100"
                    disabled
                    onclick="tambahKeKeranjang()">
                Tambahkan
            </button>
        </div>
    </div>

    <!-- ================= TABEL ================= -->
    <table class="table table-bordered text-center">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>

        <tbody id="tbody-keranjang">
            <tr id="empty-row">
                <td colspan="6">Keranjang masih kosong</td>
            </tr>
        </tbody>
    </table>

    <!-- ================= TOTAL ================= -->
    <div class="text-center my-4">
        <h5>Total</h5>
        <h4 id="display-total">Rp 0</h4>
    </div>

    <!-- ================= BAYAR ================= -->
    <div class="text-end">
        <button id="btn-bayar"
                class="btn btn-success px-5"
                disabled
                onclick="prosesBayar()">
            Bayar
        </button>
    </div>

</div>


{{-- Libraries --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ====================================================================
// STATE APLIKASI
// ====================================================================
let keranjang = [];           // array of { id_barang, nama, harga, jumlah, subtotal }
let barangDitemukan = false;  // flag apakah barang sudah ditemukan
let versiAktif = 'jquery';    // 'jquery' atau 'axios'

// Set CSRF token untuk Axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


// ====================================================================
// SWITCH VERSI
// ====================================================================
function setVersion(versi, event) {
    event.preventDefault();
    versiAktif = versi;
    document.getElementById('tab-jquery').classList.toggle('active', versi === 'jquery');
    document.getElementById('tab-axios').classList.toggle('active', versi === 'axios');
    document.getElementById('label-versi').textContent = versi === 'jquery' ? 'jQuery AJAX' : 'Axios';
    document.getElementById('label-method').textContent = versi === 'jquery' ? '$.ajax()' : 'axios.get()';
    resetFormInput();
}


// ====================================================================
// RESET FORM INPUT BARANG
// ====================================================================
function resetFormInput() {
    document.getElementById('input-kode').value = '';
    document.getElementById('input-nama').value = '';
    document.getElementById('input-harga').value = '';
    document.getElementById('input-jumlah').value = 1;
    document.getElementById('notif-barang').innerHTML = '';
    document.getElementById('btn-tambah').disabled = true;
    barangDitemukan = false;
}


// ====================================================================
// CARI BARANG — event: tekan Enter pada input kode
// ====================================================================
document.getElementById('input-kode').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const kode = this.value.trim();
        if (!kode) return;

        // Reset
        document.getElementById('input-nama').value = '';
        document.getElementById('input-harga').value = '';
        document.getElementById('input-jumlah').value = 1;
        document.getElementById('btn-tambah').disabled = true;
        barangDitemukan = false;

        if (versiAktif === 'jquery') {
            cariBarangJquery(kode);
        } else {
            cariBarangAxios(kode);
        }
    }
});


// ====================================================================
// CARI BARANG - VERSI JQUERY AJAX
// ====================================================================
function cariBarangJquery(kode) {
    const notif = $('#notif-barang');
    notif.html('<span class="text-secondary">Mencari...</span>');

    $.ajax({
        url: "{{ route('pos.cari_barang') }}",
        method: "GET",
        data: { kode: kode },
        success: function (response) {
            if (response.status === 'success') {
                tampilkanBarang(response.data);
                notif.html('<span class="text-success">✅ Barang ditemukan!</span>');
            } else {
                notif.html('<span class="text-danger">❌ Barang tidak ditemukan.</span>');
            }
        },
        error: function (xhr) {
            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
            notif.html(`<span class="text-danger">❌ ${msg}</span>`);
        }
    });
}


// ====================================================================
// CARI BARANG - VERSI AXIOS
// ====================================================================
function cariBarangAxios(kode) {
    const notif = document.getElementById('notif-barang');
    notif.innerHTML = '<span class="text-secondary">Mencari...</span>';

    axios.get("{{ route('pos.cari_barang') }}", {
        params: { kode: kode }
    })
    .then(function (response) {
        if (response.data.status === 'success') {
            tampilkanBarang(response.data.data);
            notif.innerHTML = '<span class="text-success">✅ Barang ditemukan!</span>';
        } else {
            notif.innerHTML = '<span class="text-danger">❌ Barang tidak ditemukan.</span>';
        }
    })
    .catch(function (error) {
        const msg = error.response?.data?.message || 'Terjadi kesalahan.';
        notif.innerHTML = `<span class="text-danger">❌ ${msg}</span>`;
    });
}


// ====================================================================
// TAMPILKAN DATA BARANG DITEMUKAN KE FORM
// ====================================================================
function tampilkanBarang(barang) {
    document.getElementById('input-nama').value  = barang.nama;
    document.getElementById('input-harga').value = formatRupiah(barang.harga);
    document.getElementById('input-jumlah').value = 1;
    barangDitemukan = true;
    cekTombolTambah();
}


// ====================================================================
// CEK APAKAH TOMBOL TAMBAH BOLEH AKTIF
// ====================================================================
function cekTombolTambah() {
    const jumlah = parseInt(document.getElementById('input-jumlah').value);
    document.getElementById('btn-tambah').disabled = !(barangDitemukan && jumlah > 0);
}

document.getElementById('input-jumlah').addEventListener('input', cekTombolTambah);


// ====================================================================
// TAMBAH BARANG KE KERANJANG
// ====================================================================
function tambahKeKeranjang() {
    const kode    = document.getElementById('input-kode').value.trim();
    const nama    = document.getElementById('input-nama').value;
    const hargaRaw = document.getElementById('input-harga').value.replace(/[^0-9]/g, '');
    const harga   = parseInt(hargaRaw);
    const jumlah  = parseInt(document.getElementById('input-jumlah').value);

    if (!kode || !nama || !harga || jumlah < 1) return;

    const subtotal = harga * jumlah;

    // Cek apakah kode barang sudah ada di keranjang
    const idx = keranjang.findIndex(item => item.id_barang === kode);
    if (idx >= 0) {
        // Update jumlah dan subtotal
        keranjang[idx].jumlah   += jumlah;
        keranjang[idx].subtotal  = keranjang[idx].harga * keranjang[idx].jumlah;
    } else {
        // Tambah item baru
        keranjang.push({ id_barang: kode, nama, harga, jumlah, subtotal });
    }

    renderKeranjang();
    resetFormInput();
    document.getElementById('input-kode').focus();
}


// ====================================================================
// RENDER TABEL KERANJANG
// ====================================================================
function renderKeranjang() {
    const tbody = document.getElementById('tbody-keranjang');
    tbody.innerHTML = '';

    if (keranjang.length === 0) {
        tbody.innerHTML = `
            <tr id="empty-row">
                <td colspan="6" class="text-center text-muted py-4">Keranjang masih kosong</td>
            </tr>`;
        document.getElementById('btn-bayar').disabled = true;
        updateTotal();
        return;
    }

    keranjang.forEach((item, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${item.id_barang}</td>
            <td>${item.nama}</td>
            <td>${formatRupiah(item.harga)}</td>
            <td>
                <input type="number" class="form-control form-control-sm"
                       style="width:70px"
                       value="${item.jumlah}" min="1"
                       onchange="ubahJumlah(${idx}, this.value)">
            </td>
            <td class="subtotal-cell">${formatRupiah(item.subtotal)}</td>
            <td>
                <button class="btn btn-danger btn-hapus" onclick="hapusItem(${idx})">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('btn-bayar').disabled = false;
    updateTotal();
}


// ====================================================================
// UBAH JUMLAH BARANG DI TABEL
// ====================================================================
function ubahJumlah(idx, nilaiJumlah) {
    const jumlah = parseInt(nilaiJumlah);
    if (isNaN(jumlah) || jumlah < 1) return;
    keranjang[idx].jumlah   = jumlah;
    keranjang[idx].subtotal = keranjang[idx].harga * jumlah;
    renderKeranjang();
}


// ====================================================================
// HAPUS ITEM DARI KERANJANG
// ====================================================================
function hapusItem(idx) {
    keranjang.splice(idx, 1);
    renderKeranjang();
}


// ====================================================================
// UPDATE TOTAL
// ====================================================================
function updateTotal() {
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    document.getElementById('display-total').textContent = formatRupiah(total);
}


// ====================================================================
// PROSES BAYAR
// ====================================================================
function prosesBayar() {
    if (keranjang.length === 0) return;

    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    const payload = {
        items: keranjang.map(item => ({
            id_barang: item.id_barang,
            jumlah:    item.jumlah,
            subtotal:  item.subtotal
        })),
        total: total,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    // Disable tombol bayar sementara
    const btnBayar = document.getElementById('btn-bayar');
    btnBayar.disabled = true;
    btnBayar.textContent = 'Menyimpan...';

    if (versiAktif === 'jquery') {
        bayarJquery(payload, btnBayar);
    } else {
        bayarAxios(payload, btnBayar);
    }
}


// ====================================================================
// BAYAR - VERSI JQUERY AJAX
// ====================================================================
function bayarJquery(payload, btnBayar) {
    $.ajax({
        url: "{{ route('pos.bayar') }}",
        method: "POST",
        data: payload,
        success: function (response) {
            btnBayar.disabled = false;
            btnBayar.textContent = '💳 Bayar';
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    html: `Transaksi #<strong>${response.data.id_penjualan}</strong> berhasil disimpan.<br>
                           Total: <strong>${formatRupiah(response.data.total)}</strong>`,
                    confirmButtonColor: '#198754'
                }).then(() => {
                    bersihkanHalaman();
                });
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },
        error: function (xhr) {
            btnBayar.disabled = false;
            btnBayar.textContent = '💳 Bayar';
            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan server.';
            Swal.fire('Error!', msg, 'error');
        }
    });
}


// ====================================================================
// BAYAR - VERSI AXIOS
// ====================================================================
function bayarAxios(payload, btnBayar) {
    axios.post("{{ route('pos.bayar') }}", payload)
    .then(function (response) {
        btnBayar.disabled = false;
        btnBayar.textContent = '💳 Bayar';
        if (response.data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                html: `Transaksi #<strong>${response.data.data.id_penjualan}</strong> berhasil disimpan.<br>
                       Total: <strong>${formatRupiah(response.data.data.total)}</strong>`,
                confirmButtonColor: '#198754'
            }).then(() => {
                bersihkanHalaman();
            });
        } else {
            Swal.fire('Gagal!', response.data.message, 'error');
        }
    })
    .catch(function (error) {
        btnBayar.disabled = false;
        btnBayar.textContent = '💳 Bayar';
        const msg = error.response?.data?.message || 'Terjadi kesalahan server.';
        Swal.fire('Error!', msg, 'error');
    });
}


// ====================================================================
// BERSIHKAN SEMUA DATA SETELAH BAYAR BERHASIL
// ====================================================================
function bersihkanHalaman() {
    keranjang = [];
    renderKeranjang();
    resetFormInput();
    document.getElementById('input-kode').focus();
}


// ====================================================================
// HELPER: FORMAT RUPIAH
// ====================================================================
function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

</script>
</body>
</html>