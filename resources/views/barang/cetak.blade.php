<!DOCTYPE html>
<html>
<head>
<style>
@page {
    margin: 0;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    width: 210mm;
    height: 165mm;
    background-color: #edf4b7;
}

table {
    border-spacing: 3mm 2mm;
    margin: auto;
    position: absolute;
}

td.label {
    width: 38mm;
    height: 18mm;
    border: 0.1pt solid #ccc;
    vertical-align: top;
    text-align: center;
    padding: 1mm;
    box-sizing: border-box;
    overflow: hidden;
    background-color: #ffffff;
    border-radius: 4px;
    padding-top: 2mm;
}

td.empty {
    border: 0.1pt solid #f2f2f2;
}

.barcode img {
    width: 100%;
    height: 28px;
    object-fit: contain;
    margin-bottom: 2px;
}

.nama {
    font-size: 7.5pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.1;
    margin-bottom: 2px;
    word-wrap: break-word;
}

.harga {
    font-size: 9pt;
    font-weight: bold;
    display: block;
}

.kode {
    font-size: 6pt;
    color: #666;
    margin-top: 1px;
    text-transform: uppercase;
}
</style>
</head>
<body>

@php
    $startIndex = (($y - 1) * 5) + $x - 1;
    $totalLabel = 40;
    $cols = 5;
@endphp

<table>
@for ($i = 0; $i < $totalLabel; $i++)
    @if ($i % $cols == 0)
        <tr>
    @endif

    @if($i >= $startIndex && isset($barang[$i - $startIndex]))
        @php $item = $barang[$i - $startIndex]; @endphp
        <td class="label">
            <div class="nama">{{ $item->nama }}</div>
            <div class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
            <div class="kode">{{ $item->id_barang }}</div>
            @if(isset($item->barcode))
                <img src="data:image/png;base64,{{ $item->barcode }}"
                    style="width: 90%; height: 20px; object-fit: contain; margin-bottom: 2px;">
            @endif
        </td>
    @else
        <td class="label empty">&nbsp;</td>
    @endif

    @if ($i % $cols == $cols - 1)
        </tr>
    @endif
@endfor

</table>

</body>
</html>