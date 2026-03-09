@extends('layout.main')

@section('style-page')
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 38px;
        padding: 5px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endsection

@section('content')

<div class="container">

        <!-- CARD INPUT KOTA -->
        <div class="card p-3 mb-4">

            <div class="row">

                <div class="col-md-8">
                    <label class="form-label">Kota:</label>
                    <input type="text" id="inputKota" class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success w-100" id="btnTambah">
                        Tambahkan
                    </button>
                </div>

            </div>

        </div>


        <!-- CARD SELECT BIASA -->
        <div class="card p-3">

            <h5 class="mb-3">Select</h5>

            <label class="form-label">Select Kota:</label>

            <select id="selectKota" class="form-control mb-3">
                <option value="">Pilih Kota</option>
            </select>

            <p>
                Kota Terpilih :
                <span id="hasil"></span>
            </p>

        </div>


        <!-- CARD SELECT2 -->
        <div class="card p-3">

            <h5 class="mb-3">Select 2</h5>

            <label class="form-label">Select Kota:</label>

            <select id="selectKota2" class="form-control mb-3">
                <option value="">Pilih Kota</option>
            </select>

            <p>
                Kota Terpilih :
                <span id="hasil2"></span>
            </p>

        </div>

    </div>
@endsection


@section('js-page')



    <script>

        $(document).ready(function () {

            // aktifkan select2
            $('#selectKota2').select2();

            // tombol tambah kota
            $(document).on("click","#btnTambah",function() {

                let kota = $("#inputKota").val();

                if (kota == "") {
                    alert("Kota harus diisi");
                    return;
                }

                let option = `<option value="${kota}">${kota}</option>`;

                $("#selectKota").append(option);
                $("#selectKota2").append(option);

                $("#inputKota").val("");

            });


            // select biasa
            $("#selectKota").change(function () {

                let kota = $(this).val();

                $("#hasil").text(kota);

            });


            // select2
            $("#selectKota2").change(function () {

                let kota = $(this).val();

                $("#hasil2").text(kota);

            });

        });

    </script>
    @endsection
