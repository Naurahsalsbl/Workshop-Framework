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
                        ID Barang
                    </label>

                    <div class="col-md-6">
                        <input
                            type="text"
                            id="idBarangInput"
                            class="form-control"
                            readonly>
                    </div>

                </div>

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
            <div class="modal fade" id="modalBarang" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Barang</h5>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                        <label>ID Barang</label>
                        <input type="text" id="editId" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" id="editNama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                        <label>Harga Barang</label>
                        <input type="number" id="editHarga" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" id="btnHapus">Hapus</button>
                        <button class="btn btn-primary" id="btnUpdate">Ubah</button>
                    </div>

                    </div>
                </div>
                </div>

        </div>

    </div>

</div>
@endsection


@section('js-page')
<script>

let idBarang = 1;
let selectedRow = null;

$(document).ready(function(){

    /* aktifkan datatables */
    let table = $('#tabelBarang').DataTable();

    $("#submitBtn").click(function(){

        let form = document.getElementById("formBarang");

        if(!form.checkValidity()){
            form.reportValidity();
            return;
        }

        let nama = $("#nama").val();
        let harga = $("#harga").val();

        /* tambah data ke datatables */

        table.row.add([
            idBarang++,
            nama,
            harga
        ]).draw();

        $("#nama").val("");
        $("#harga").val("");

    });


    /* klik row tabel */

    $('#tabelBarang tbody').on('click','tr',function(){

        selectedRow = table.row(this);

        let data = selectedRow.data();

        $("#editId").val(data[0]);
        $("#editNama").val(data[1]);
        $("#editHarga").val(data[2]);

        $("#modalBarang").modal("show");

    });



    /* update data */

    $("#btnUpdate").click(function(){

        let id = $("#editId").val();
        let nama = $("#editNama").val();
        let harga = $("#editHarga").val();

        selectedRow.data([
            id,
            nama,
            harga
        ]).draw();

        $("#modalBarang").modal("hide");

    });



    /* hapus data */

    $("#btnHapus").click(function(){

        selectedRow.remove().draw();

        $("#modalBarang").modal("hide");

    });

});

</script>
@endsection