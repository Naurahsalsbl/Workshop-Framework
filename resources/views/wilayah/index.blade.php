<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studi Kasus 1 - Dropdown Wilayah</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        .badge-version { font-size: 0.75rem; }
        select:disabled { background-color: #e9ecef; cursor: not-allowed; }
        .result-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h3 class="mb-1 fw-bold">Studi Kasus 1</h3>
    <p class="text-muted mb-4">Dropdown Wilayah Administrasi Indonesia</p>

    <div class="row g-4">

        {{-- ============================================================ --}}
        {{-- VERSI 1: jQuery AJAX --}}
        {{-- ============================================================ --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <strong>Versi jQuery AJAX</strong>
                    <span class="badge bg-light text-primary badge-version ms-2">$.ajax()</span>
                </div>
                <div class="card-body">

                    {{-- Provinsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Provinsi</label>
                        <select id="jquery-provinsi" class="form-select">
                            <option value="0">-- Pilih Provinsi --</option>
                            @foreach($provinsi as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kota --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kota / Kabupaten</label>
                        <select id="jquery-kota" class="form-select" disabled>
                            <option value="0">-- Pilih Kota --</option>
                        </select>
                    </div>

                    {{-- Kecamatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kecamatan</label>
                        <select id="jquery-kecamatan" class="form-select" disabled>
                            <option value="0">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    {{-- Kelurahan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelurahan / Desa</label>
                        <select id="jquery-kelurahan" class="form-select" disabled>
                            <option value="0">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    {{-- Hasil Pilihan --}}
                    <div id="jquery-result" class="result-box d-none">
                        <strong>Hasil Pilihan:</strong>
                        <div id="jquery-result-text" class="mt-1 text-success"></div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- VERSI 2: Axios --}}
        {{-- ============================================================ --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <strong>Versi Axios</strong>
                    <span class="badge bg-light text-success badge-version ms-2">axios.get()</span>
                </div>
                <div class="card-body">

                    {{-- Provinsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Provinsi</label>
                        <select id="axios-provinsi" class="form-select">
                            <option value="0">-- Pilih Provinsi --</option>
                            @foreach($provinsi as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kota --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kota / Kabupaten</label>
                        <select id="axios-kota" class="form-select" disabled>
                            <option value="0">-- Pilih Kota --</option>
                        </select>
                    </div>

                    {{-- Kecamatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kecamatan</label>
                        <select id="axios-kecamatan" class="form-select" disabled>
                            <option value="0">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    {{-- Kelurahan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelurahan / Desa</label>
                        <select id="axios-kelurahan" class="form-select" disabled>
                            <option value="0">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    {{-- Hasil Pilihan --}}
                    <div id="axios-result" class="result-box d-none">
                        <strong>Hasil Pilihan:</strong>
                        <div id="axios-result-text" class="mt-1 text-success"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- end row --}}
</div>{{-- end container --}}

{{-- ================================================================ --}}
{{-- LIBRARIES --}}
{{-- ================================================================ --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ====================================================================
// =====================  VERSI JQUERY AJAX  ==========================
// ====================================================================

// Helper: reset select dan disable
function jqueryResetSelect(selector, placeholder) {
    $(selector).html('<option value="0">' + placeholder + '</option>').prop('disabled', true);
}

// --- Event: Pilih Provinsi ---
$('#jquery-provinsi').on('change', function () {
    const provinceId = $(this).val();

    // Reset level 2, 3, 4
    jqueryResetSelect('#jquery-kota', '-- Pilih Kota --');
    jqueryResetSelect('#jquery-kecamatan', '-- Pilih Kecamatan --');
    jqueryResetSelect('#jquery-kelurahan', '-- Pilih Kelurahan --');
    $('#jquery-result').addClass('d-none');

    if (provinceId == 0) return;

    // AJAX ke server untuk ambil data kota
    $.ajax({
        url: "{{ route('wilayah.kota') }}",
        method: "GET",
        data: { province_id: provinceId },
        success: function (response) {
            if (response.status === 'success') {
                let options = '<option value="0">-- Pilih Kota --</option>';
                $.each(response.data, function (i, kota) {
                    options += `<option value="${kota.id}">${kota.name}</option>`;
                });
                $('#jquery-kota').html(options).prop('disabled', false);
            }
        },
        error: function (xhr) {
            console.log('Error ambil kota:', xhr);
            Swal.fire('Error!', 'Gagal mengambil data kota.', 'error');
        }
    });
});

// --- Event: Pilih Kota ---
$('#jquery-kota').on('change', function () {
    const regencyId = $(this).val();

    // Reset level 3 dan 4
    jqueryResetSelect('#jquery-kecamatan', '-- Pilih Kecamatan --');
    jqueryResetSelect('#jquery-kelurahan', '-- Pilih Kelurahan --');
    $('#jquery-result').addClass('d-none');

    if (regencyId == 0) return;

    $.ajax({
        url: "{{ route('wilayah.kecamatan') }}",
        method: "GET",
        data: { regency_id: regencyId },
        success: function (response) {
            if (response.status === 'success') {
                let options = '<option value="0">-- Pilih Kecamatan --</option>';
                $.each(response.data, function (i, kec) {
                    options += `<option value="${kec.id}">${kec.name}</option>`;
                });
                $('#jquery-kecamatan').html(options).prop('disabled', false);
            }
        },
        error: function (xhr) {
            console.log('Error ambil kecamatan:', xhr);
            Swal.fire('Error!', 'Gagal mengambil data kecamatan.', 'error');
        }
    });
});

// --- Event: Pilih Kecamatan ---
$('#jquery-kecamatan').on('change', function () {
    const districtId = $(this).val();

    // Reset level 4
    jqueryResetSelect('#jquery-kelurahan', '-- Pilih Kelurahan --');
    $('#jquery-result').addClass('d-none');

    if (districtId == 0) return;

    $.ajax({
        url: "{{ route('wilayah.kelurahan') }}",
        method: "GET",
        data: { district_id: districtId },
        success: function (response) {
            if (response.status === 'success') {
                let options = '<option value="0">-- Pilih Kelurahan --</option>';
                $.each(response.data, function (i, kel) {
                    options += `<option value="${kel.id}">${kel.name}</option>`;
                });
                $('#jquery-kelurahan').html(options).prop('disabled', false);
            }
        },
        error: function (xhr) {
            console.log('Error ambil kelurahan:', xhr);
            Swal.fire('Error!', 'Gagal mengambil data kelurahan.', 'error');
        }
    });
});

// --- Event: Pilih Kelurahan (tampilkan hasil) ---
$('#jquery-kelurahan').on('change', function () {
    if ($(this).val() == 0) {
        $('#jquery-result').addClass('d-none');
        return;
    }
    const prov  = $('#jquery-provinsi option:selected').text();
    const kota  = $('#jquery-kota option:selected').text();
    const kec   = $('#jquery-kecamatan option:selected').text();
    const kel   = $('#jquery-kelurahan option:selected').text();

    $('#jquery-result-text').html(`${kel}, ${kec}, ${kota}, ${prov}`);
    $('#jquery-result').removeClass('d-none');
});


// ====================================================================
// =======================  VERSI AXIOS  ==============================
// ====================================================================

// Helper: reset select dan disable (versi vanilla untuk Axios)
function axiosResetSelect(id, placeholder) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="0">${placeholder}</option>`;
    el.disabled = true;
}

// --- Event: Pilih Provinsi ---
document.getElementById('axios-provinsi').addEventListener('change', function () {
    const provinceId = this.value;

    axiosResetSelect('axios-kota', '-- Pilih Kota --');
    axiosResetSelect('axios-kecamatan', '-- Pilih Kecamatan --');
    axiosResetSelect('axios-kelurahan', '-- Pilih Kelurahan --');
    document.getElementById('axios-result').classList.add('d-none');

    if (provinceId == 0) return;

    axios.get("{{ route('wilayah.kota') }}", {
        params: { province_id: provinceId }
    })
    .then(function (response) {
        if (response.data.status === 'success') {
            const kotaEl = document.getElementById('axios-kota');
            let options = '<option value="0">-- Pilih Kota --</option>';
            response.data.data.forEach(kota => {
                options += `<option value="${kota.id}">${kota.name}</option>`;
            });
            kotaEl.innerHTML = options;
            kotaEl.disabled = false;
        }
    })
    .catch(function (error) {
        console.log('Error ambil kota:', error);
        Swal.fire('Error!', 'Gagal mengambil data kota.', 'error');
    });
});

// --- Event: Pilih Kota ---
document.getElementById('axios-kota').addEventListener('change', function () {
    const regencyId = this.value;

    axiosResetSelect('axios-kecamatan', '-- Pilih Kecamatan --');
    axiosResetSelect('axios-kelurahan', '-- Pilih Kelurahan --');
    document.getElementById('axios-result').classList.add('d-none');

    if (regencyId == 0) return;

    axios.get("{{ route('wilayah.kecamatan') }}", {
        params: { regency_id: regencyId }
    })
    .then(function (response) {
        if (response.data.status === 'success') {
            const kecEl = document.getElementById('axios-kecamatan');
            let options = '<option value="0">-- Pilih Kecamatan --</option>';
            response.data.data.forEach(kec => {
                options += `<option value="${kec.id}">${kec.name}</option>`;
            });
            kecEl.innerHTML = options;
            kecEl.disabled = false;
        }
    })
    .catch(function (error) {
        console.log('Error ambil kecamatan:', error);
        Swal.fire('Error!', 'Gagal mengambil data kecamatan.', 'error');
    });
});

// --- Event: Pilih Kecamatan ---
document.getElementById('axios-kecamatan').addEventListener('change', function () {
    const districtId = this.value;

    axiosResetSelect('axios-kelurahan', '-- Pilih Kelurahan --');
    document.getElementById('axios-result').classList.add('d-none');

    if (districtId == 0) return;

    axios.get("{{ route('wilayah.kelurahan') }}", {
        params: { district_id: districtId }
    })
    .then(function (response) {
        if (response.data.status === 'success') {
            const kelEl = document.getElementById('axios-kelurahan');
            let options = '<option value="0">-- Pilih Kelurahan --</option>';
            response.data.data.forEach(kel => {
                options += `<option value="${kel.id}">${kel.name}</option>`;
            });
            kelEl.innerHTML = options;
            kelEl.disabled = false;
        }
    })
    .catch(function (error) {
        console.log('Error ambil kelurahan:', error);
        Swal.fire('Error!', 'Gagal mengambil data kelurahan.', 'error');
    });
});

// --- Event: Pilih Kelurahan (tampilkan hasil) ---
document.getElementById('axios-kelurahan').addEventListener('change', function () {
    const resultEl = document.getElementById('axios-result');
    if (this.value == 0) {
        resultEl.classList.add('d-none');
        return;
    }
    const prov = document.querySelector('#axios-provinsi option:checked').text;
    const kota = document.querySelector('#axios-kota option:checked').text;
    const kec  = document.querySelector('#axios-kecamatan option:checked').text;
    const kel  = document.querySelector('#axios-kelurahan option:checked').text;

    document.getElementById('axios-result-text').innerHTML = `${kel}, ${kec}, ${kota}, ${prov}`;
    resultEl.classList.remove('d-none');
});

</script>
</body>
</html>