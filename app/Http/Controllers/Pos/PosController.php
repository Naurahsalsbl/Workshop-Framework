<?php

namespace App\Http\Controllers\Pos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PosController extends Controller
{
    /**
     * Tampilkan halaman POS/Kasir
     * Route: GET /pos
     */
    public function index()
    {
        return view('pos.index');
    }

    /**
     * Cari barang berdasarkan kode (dipanggil saat Enter di input kode)
     * Route: GET /pos/cari-barang?kode=xxx
     */
    public function cariBarang(Request $request)
    {
        $kode = $request->get('kode');

        if (!$kode) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Kode barang tidak boleh kosong',
                'data'    => null
            ], 400);
        }

        $barang = DB::table('barang')->where('id_barang', $kode)->first();

        if (!$barang) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Barang tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Barang ditemukan',
            'data'    => $barang
        ]);
    }

    /**
     * Simpan transaksi penjualan ke database
     * Route: POST /pos/bayar
     */
    public function bayar(Request $request)
    {
        $items = $request->input('items'); // array of {id_barang, jumlah, subtotal}
        $total = $request->input('total');

        if (!$items || count($items) === 0) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Tidak ada item untuk disimpan',
                'data'    => null
            ], 400);
        }

        // Simpan ke tabel penjualan
        $id_penjualan = DB::table('penjualan')->insertGetId([
            'timestamp' => now(),
            'total'     => $total
        ]);

        // Simpan detail ke tabel penjualan_detail
        $details = [];
        foreach ($items as $item) {
            $details[] = [
                'id_penjualan' => $id_penjualan,
                'id_barang'    => $item['id_barang'],
                'jumlah'       => $item['jumlah'],
                'subtotal'     => $item['subtotal'],
            ];
        }

        DB::table('penjualan_detail')->insert($details);

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Transaksi berhasil disimpan',
            'data'    => [
                'id_penjualan' => $id_penjualan,
                'total'        => $total,
                'jumlah_item'  => count($items)
            ]
        ]);
    }
}