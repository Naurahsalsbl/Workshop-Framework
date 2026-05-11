<!DOCTYPE html>
<html>
<head>
    <style>

        body{
            text-align:center;
            font-family:Arial;
            margin-top:40px;
        }

        img{
            width:450px;
            height:120px;
        }

        h2{
            margin-top:20px;
            margin-bottom:5px;
        }

        p{
            font-size:20px;
            letter-spacing:2px;
        }

    </style>
</head>
<body>

    <img src="data:image/png;base64,{{ $barcode }}">

    <h2>{{ $toko->nama_toko }}</h2>

    <p>{{ $toko->barcode }}</p>

</body>
</html>