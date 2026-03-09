<!DOCTYPE html>
<html>
<head>
<style>
@page {
    size: A4;
    margin: 0;
}

body {
    margin: 0;
    padding: 0;
    width: 210mm;
    height: 297mm;          /*167*/
    font-family: Arial, sans-serif;
    background-color: #ffffff;
}
table {
    height: 200mm;
    width: 190mm;
    margin-left: 10mm;  /* center horizontal */
    margin-top: 8.5mm;
    border-collapse: collapse;
    table-layout: fixed;
}
td.label {
    width: 38mm;
    height: 25mm;
    padding: 3mm;
    font-size: 10px;
    box-sizing: border-box;
    overflow: hidden;
    background-color: #ffffff;
    vertical-align: middle;
    text-align: center;
}
.nama {
    font-weight: bold;
    font-size: 11px;
}
.harga {
    margin-top: 3px;
    font-size: 10px;
}
.kode {
    margin-top: 3px;
    font-size: 9px;
    color: #555;
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
            <div class="kode">{{ $item->kode ?? '' }}</div>
        </td>
    @else
        <td class="label"></td>
    @endif

    @if ($i % $cols == $cols - 1)
        </tr>
    @endif
@endfor
</table>

</body>
</html>