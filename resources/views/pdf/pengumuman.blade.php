<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 50px;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .content {
            text-align: justify;
            line-height: 1.6;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>FAKULTAS VOKASI</h2>
        <p>Universitas Airlangga</p>
    </div>

    <div class="content">
        <h3 style="text-align:center;">PENGUMUMAN 2</h3>

        <p>
            Diberitahukan kepada seluruh mahasiswa bahwa kegiatan perkuliahan
            akan dilaksanakan sesuai jadwal.
        </p>

        <p>
            Demikian pengumuman ini disampaikan.
        </p>
    </div>

    <div class="footer">
        <p> {{ date('d F Y') }}</p>
    </div>

</body>
</html>
