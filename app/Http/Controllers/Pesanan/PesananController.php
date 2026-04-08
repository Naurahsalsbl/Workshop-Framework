<?php

namespace App\Http\Controllers\Pesanan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PesananController extends Controller
{
    // Halaman utama pemesanan
    public function index()
    {
        $vendors = DB::table('vendor')->get();
        return view('customer.order', compact('vendors'));
    }

    // Ambil menu berdasarkan vendor (AJAX)
    public function getMenu($idvendor)
    {
        try {
            $menus = DB::table('menu')
                ->where('idvendor', $idvendor)
                ->get();

            return response()->json($menus);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal load menu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Buat nama guest otomatis
    private function generateGuestName()
    {
        $last = DB::table('pesanan')
            ->where('nama', 'like', 'Guest_%')
            ->orderBy('idpesanan', 'desc')
            ->first();

        if (!$last) {
            return 'Guest_0000001';
        }

        $number = (int) substr($last->nama, 6) + 1;
        return 'Guest_' . str_pad($number, 7, '0', STR_PAD_LEFT);
    }

    // Simpan pesanan
    public function store(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'idvendor' => 'required',
                'items'    => 'required|array|min:1',
            ]);

            $guestName = $this->generateGuestName();
            $total = 0;

            // Hitung total dan cek menu
            foreach ($request->items as $item) {
                $menu = DB::table('menu')->where('idmenu', $item['idmenu'])->first();
                if (!$menu) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Menu tidak ditemukan: ' . $item['idmenu']
                    ], 404);
                }
                $total += $menu->harga * $item['jumlah'];
            }

            // Simpan pesanan (tanpa insertGetId karena primary key bukan 'id')
            DB::table('pesanan')->insert([
                'nama'         => $guestName,
                'total'        => $total,
                'status_bayar' => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Ambil id pesanan yang baru saja disimpan
            $idpesanan = DB::table('pesanan')
                ->where('nama', $guestName)
                ->orderBy('idpesanan', 'desc')
                ->value('idpesanan');

            // Simpan detail pesanan
            foreach ($request->items as $item) {
                $menu = DB::table('menu')->where('idmenu', $item['idmenu'])->first();
                DB::table('detail_pesanan')->insert([
                    'idpesanan'  => $idpesanan,
                    'idmenu'     => $item['idmenu'],
                    'jumlah'     => $item['jumlah'],
                    'harga'      => $menu->harga,
                    'subtotal'   => $menu->harga * $item['jumlah'],
                    'timestamp'  => now(),
                ]);
            }

            return response()->json([
                'success'    => true,
                'idpesanan'  => $idpesanan,
                'nama'       => $guestName,
                'total'      => $total,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}