@extends('layout.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-cart"></i>
        </span>
        Pemesanan Kantin
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pemesanan</li>
        </ol>
    </nav>
</div>

<div class="row">
    {{-- Kolom Kiri: Pilih Vendor & Menu --}}
    <div class="col-lg-7 col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-store text-primary me-2"></i>Pilih Kantin
                </h4>

                {{-- Pilih Vendor --}}
                <div class="form-group">
                    <label class="fw-bold">Pilih Vendor</label>
                    <select id="vendorSelect" class="form-control form-control-lg">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Loading --}}
                <div id="menuLoading" style="display:none" class="text-center mt-4 py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat menu...</p>
                </div>

                {{-- Daftar Menu --}}
                <div id="menuSection" style="display:none" class="mt-4">
                    <h5 class="fw-bold mb-3">
                        <i class="mdi mdi-food text-success me-1"></i>Pilih Menu
                    </h5>
                    <div id="menuList"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Keranjang --}}
    <div class="col-lg-5 col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-cart text-primary me-2"></i>Pesanan Kamu
                </h4>

                {{-- Keranjang Kosong --}}
                <div id="cartEmpty" class="text-center py-5">
                    <i class="mdi mdi-cart-off" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">Keranjang masih kosong</p>
                </div>

                {{-- Isi Keranjang --}}
                <div id="cartContent" style="display:none">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary">Rp <span id="totalHarga">0</span></span>
                    </div>

                    <button onclick="pesanDanBayar()" class="btn btn-gradient-primary btn-lg w-100">
                        <i class="mdi mdi-credit-card me-2"></i>Pesan & Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let selectedVendor = null;

    document.getElementById('vendorSelect').addEventListener('change', function () {
        selectedVendor = this.value;
        if (!selectedVendor) {
            document.getElementById('menuSection').style.display = 'none';
            return;
        }

        document.getElementById('menuLoading').style.display = 'block';
        document.getElementById('menuSection').style.display = 'none';

        fetch(`/order/menu/${selectedVendor}`)
            .then(res => res.json())
            .then(menus => {
                document.getElementById('menuLoading').style.display = 'none';

                if (menus.length === 0) {
                    document.getElementById('menuList').innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="mdi mdi-food-off" style="font-size:32px"></i>
                            <p>Belum ada menu tersedia</p>
                        </div>`;
                } else {
                    let html = '';
                    menus.forEach(menu => {
                        const gambar = menu.path_gambar
                            ? `<img src="/storage/${menu.path_gambar}" style="width:60px; height:60px; object-fit:cover; border-radius:8px;">`
                            : `<div class="d-flex align-items-center justify-content-center bg-secondary" style="width:60px; height:60px; border-radius:8px;">
                                <i class="mdi mdi-food text-white" style="font-size:24px;"></i>
                               </div>`;

                        html += `
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-2" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center gap-3">
                                ${gambar}
                                <div>
                                    <div class="fw-bold">${menu.nama_menu}</div>
                                    <div class="text-primary fw-semibold">Rp ${Number(menu.harga).toLocaleString('id-ID')}</div>
                                </div>
                            </div>
                            <button onclick="addToCart(${menu.idmenu}, '${menu.nama_menu}', ${menu.harga})"
                                class="btn btn-gradient-success btn-sm">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>`;
                    });
                    document.getElementById('menuList').innerHTML = html;
                }

                document.getElementById('menuSection').style.display = 'block';
            })
            .catch(() => {
                document.getElementById('menuLoading').style.display = 'none';
                document.getElementById('menuList').innerHTML = `<div class="text-danger">Gagal memuat menu.</div>`;
                document.getElementById('menuSection').style.display = 'block';
            });
    });

    function addToCart(idmenu, nama, harga) {
        let existing = cart.find(i => i.idmenu == idmenu);
        if (existing) {
            existing.jumlah++;
        } else {
            cart.push({ idmenu, nama, harga, jumlah: 1 });
        }
        renderCart();
    }

    function removeFromCart(idmenu) {
        cart = cart.filter(i => i.idmenu != idmenu);
        renderCart();
    }

    function changeQty(idmenu, delta) {
        let item = cart.find(i => i.idmenu == idmenu);
        if (!item) return;
        item.jumlah += delta;
        if (item.jumlah <= 0) {
            cart = cart.filter(i => i.idmenu != idmenu);
        }
        renderCart();
    }

    function renderCart() {
        let total = 0;
        let html = '';

        cart.forEach(item => {
            let subtotal = item.harga * item.jumlah;
            total += subtotal;
            html += `
                <tr>
                    <td>
                        <div class="fw-semibold">${item.nama}</div>
                        <small class="text-muted">Rp ${Number(item.harga).toLocaleString('id-ID')}</small>
                    </td>
                    <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button onclick="changeQty(${item.idmenu}, -1)" class="btn btn-outline-secondary btn-sm px-2 py-0">-</button>
                            <span class="fw-bold px-1">${item.jumlah}</span>
                            <button onclick="changeQty(${item.idmenu}, 1)" class="btn btn-outline-secondary btn-sm px-2 py-0">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-semibold">Rp ${Number(subtotal).toLocaleString('id-ID')}</td>
                    <td class="text-center">
                        <button onclick="removeFromCart(${item.idmenu})" class="btn btn-outline-danger btn-sm px-1 py-0">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById('cartBody').innerHTML = html;
        document.getElementById('totalHarga').innerText = Number(total).toLocaleString('id-ID');

        if (cart.length > 0) {
            document.getElementById('cartEmpty').style.display = 'none';
            document.getElementById('cartContent').style.display = 'block';
        } else {
            document.getElementById('cartEmpty').style.display = 'block';
            document.getElementById('cartContent').style.display = 'none';
        }
    }

    function pesanDanBayar() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        fetch('/order/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                idvendor: selectedVendor,
                items: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/payment/${data.idpesanan}`;
            } else {
                alert('Gagal membuat pesanan: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(() => alert('Terjadi kesalahan, coba lagi.'));
    }
</script>
@endsection