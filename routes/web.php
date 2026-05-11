<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Barang\BarangController;
use App\Http\Controllers\Buku\BukuController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Kategori\KategoriController;
use App\Http\Controllers\Menu\MenuController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\Pesanan\PesananController;
use App\Http\Controllers\Pesanan\PesananVendorController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor\VendorAuthController;
use App\Http\Controllers\Wilayah\WilayahController;
use App\Http\Controllers\Toko\TokoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/otp', [OtpController::class, 'show'])->name('otp.form');
Route::post('/otp', [OtpController::class, 'verify'])->name('otp.verify');


// Google Login
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Redirect root ke dashboard
Route::redirect('/', '/dashboard');

// Dashboard (wajib login + verified)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Profile (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');

Route::get('/sertifikat', [PdfController::class, 'sertifikat']);
Route::get('/pengumuman', [PdfController::class, 'pengumuman']);

Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
Route::get('/barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
Route::post('/barang/cetak-label', [BarangController::class, 'cetaklabel'])->name('cetak.label');

Route::get('/api/barang/{id}', [BarangController::class, 'getBarang']);

Route::get('/scan', function () {
    return view('barang.scan');
})->name('barang.scan');

Route::get('/tugas/barang', function () {
    return view('tugas.barang');
})->name('tugas.barang');

Route::get('/tugas/html', function () {
    return view('tugas.html');
})->name('tugas.html');

Route::get('/tugas/datatable', function () {
    return view('tugas.datatable');
})->name('tugas.datatable');

Route::get('/tugas/kota', function () {
    return view('tugas.kota');
})->name('tugas.kota');

Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
Route::get('/wilayah/kota', [WilayahController::class, 'getKota'])->name('wilayah.kota');
Route::get('/wilayah/kecamatan', [WilayahController::class, 'getKecamatan'])->name('wilayah.kecamatan');
Route::get('/wilayah/kelurahan', [WilayahController::class, 'getKelurahan'])->name('wilayah.kelurahan');

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::get('/pos/cari-barang', [PosController::class, 'cariBarang'])->name('pos.cari_barang');
Route::post('/pos/bayar', [PosController::class, 'bayar'])->name('pos.bayar');

// Vendor Auth
//Route::get('/vendor/login', [VendorAuthController::class, 'showLogin'])->name('vendor.login');
Route::post('/vendor/login', [VendorAuthController::class, 'login'])->name('vendor.login.post');
Route::post('/vendor/logout', [VendorAuthController::class, 'logout'])->name('vendor.logout');
Route::get('/vendor/masuk/{vendor_id}', [VendorAuthController::class,'masukSebagaiVendor'])->name('vendor.masuk');

//Vendor Protected Routes
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', function () {
        if (!session('vendor_id')) {
            return redirect()->route('login');
        }
        return view('vendor.dashboard');
    })->name('dashboard');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu/store', [MenuController::class, 'store'])->name('menu.store');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

    Route::get('/pesanan', [PesananVendorController::class, 'index'])->name('pesanan.index');

});

// Halaman pemesanan
Route::get('/order', [PesananController::class, 'index'])->name('customer.order.index');

// Ambil menu vendor via AJAX
Route::get('/order/menu/{idvendor}', [PesananController::class, 'getMenu'])->name('customer.order.getMenu');

// Simpan pesanan via AJAX
Route::post('/order/store', [PesananController::class, 'store'])->name('customer.order.store');


Route::get('/payment/{idpesanan}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/notification', [PaymentController::class, 'handleNotification'])->name('payment.notification');
Route::get('/payment/detail/{id}', [PaymentController::class, 'detail']);
Route::get('/payment/success/{id}', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/update-status', [PaymentController::class, 'updateStatus']);
Route::get('/customer/qr', [PaymentController::class, 'showQR']);
Route::get('/api/pesanan/{id}', [PaymentController::class, 'apiDetail']);

Route::get('/vendor/scan', function () {
    return view('vendor.scan');
})->name('vendor.scan');


Route::get('/scanner', function () {
    return view('scanner.index');
})->name('scanner.index');

Route::get('/customer', [CustomerController::class, 'index'])->name('cust.index');
Route::get('/customer/create1', [CustomerController::class, 'create1']);
Route::post('/customer/store1', [CustomerController::class, 'store1']);
Route::get('/customer/create2', [CustomerController::class, 'create2']);
Route::post('/customer/store2', [CustomerController::class, 'store2']);
Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);
Route::post('/customer/checkout', [CustomerController::class, 'checkout']);
Route::post('/customer/update-status', [CustomerController::class, 'updateStatus']);


Route::get('/', [TokoController::class, 'index'])->name('toko.index');
Route::get('/create', [TokoController::class, 'create'])->name('toko.create');
Route::post('/', [TokoController::class, 'store'])->name('toko.store');
Route::post('/validasi', [TokoController::class, 'validasiKunjungan'])->name('toko.validasi');
Route::post('/{id}/titik-awal', [TokoController::class, 'simpanTitikAwal'])->name('toko.titik-awal');
Route::get('/barcode/{barcode}', [TokoController::class, 'findByBarcode'])->name('toko.find-barcode');
Route::get('/{id}/cetak-barcode', [TokoController::class, 'cetakBarcode'])->name('toko.cetak-barcode');
Route::get('/kunjungan/scanner', function () {
    return view('toko.scanner');
})->name('toko.scanner');





// Auth routes dari Breeze
require __DIR__.'/auth.php';

