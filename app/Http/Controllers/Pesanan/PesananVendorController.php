<?php

namespace App\Http\Controllers\Pesanan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PesananVendorController extends Controller
{
    public function index()
    {
        if (!session('vendor_id')) {
            return redirect()->route('vendor.login');
        }

        $pesanan = DB::table('pesanan')
            ->join('detail_pesanan', 'pesanan.idpesanan', '=', 'detail_pesanan.idpesanan')
            ->join('menu', 'detail_pesanan.idmenu', '=', 'menu.idmenu')
            ->where('menu.idvendor', session('vendor_id'))
            ->where('pesanan.status_bayar', 1)
            ->select(
                'pesanan.idpesanan',
                'pesanan.nama',
                'pesanan.total',
                'pesanan.created_at',
                DB::raw('STRING_AGG(menu.nama_menu || \' x\' || detail_pesanan.jumlah, \', \') as items')
            )
            ->groupBy('pesanan.idpesanan', 'pesanan.nama', 'pesanan.total', 'pesanan.created_at')
            ->orderBy('pesanan.created_at', 'desc')
            ->get();

        return view('vendor.pesanan.index', compact('pesanan'));
    }
}