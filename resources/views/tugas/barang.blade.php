@extends('layout.main')

@section('content')

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header">
            <h4 class="mb-0">Form Tambah Barang</h4>
        </div>
        <div class="card-body">
            <form id="formBarang">
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">
                        Nama Barang
                    </label>
                    <div class="col-md-6">
                        <input
                            type="text"
                            id="nama"
                            class="form-control"
                            placeholder="Masukkan nama barang"
                            required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">
                        Harga Barang
                    </label>
                    <div class="col-md-6">
                        <input
                            type="number"
                            id="harga"
                            class="form-control"
                            placeholder="Masukkan harga barang"
                            required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 offset-md-2">
                        <button
                            type="button"
                            id="submitBtn"
                            class="btn btn-primary">
                            <span id="btnText">
                                Submit
                            </span>
                            <span
                                id="btnLoader"
                                class="spinner-border spinner-border-sm d-none"> 
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <div class="card shadow mt-4">

        <div class="card-header">
            <h5 class="mb-0">Daftar Barang</h5>
        </div>

        <div class="card-body">

            <table
                class="table table-bordered table-hover"
                id="tabelBarang">

                <thead>

                    <tr>
                        <th width="80">ID</th>
                        <th>Nama Barang</th>
                        <th width="150">Harga</th>
                    </tr>

                </thead>

                <tbody>
                </tbody>

            </table>

        </div>

    </div>

</div>
@endsection


@section('js-page')
<script>

let idBarang = 1;

$("#submitBtn").on("click", function(){

    let form = document.getElementById("formBarang");

    if(!form.checkValidity()){
        form.reportValidity();
        return;
    }


    // aktifkan spinner
    $("#btnText").addClass("d-none");
    $("#btnLoader").removeClass("d-none");


    setTimeout(function(){

        let namaBarang  = $("#nama").val();
        let hargaBarang = $("#harga").val();

        let row = `
            <tr>
                <td>${idBarang++}</td>
                <td>${namaBarang}</td>
                <td>${hargaBarang}</td>
            </tr>
        `;

        $("#tabelBarang tbody").append(row);

        // reset input
        $("#nama").val("");
        $("#harga").val("");


        // matikan spinner
        $("#btnLoader").addClass("d-none");
        $("#btnText").removeClass("d-none");

    },800);

});

</script>

@endsection