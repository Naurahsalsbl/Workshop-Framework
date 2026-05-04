<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CustomerController extends Controller
{

    public function index()
    {
        $customer = DB::table('customer')->get();
        return view('cust.index', compact('customer'));
    }

    public function create1()
    {
        return view('cust.create1');
    }

    public function store1(Request $request)
    {
        $image = $request->foto;

        // hapus prefix base64
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = base64_decode($image);

        // FIX BYTEA PostgreSQL
        $image = DB::raw("E'\\\\x" . bin2hex($image) . "'");

        DB::table('customer')->insert([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kodepos' => $request->kodepos,
            'foto' => $image
        ]);

        return redirect('/customer');
    }

    public function create2()
    {
        return view('cust.create2');
    }

    public function store2(Request $request)
    {
        $image = $request->foto;

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = base64_decode($image);

        $fileName = 'customer_' . time() . '.png';

        // simpan ke folder public/uploads
        file_put_contents(public_path('uploads/'.$fileName), $image);

        DB::table('customer')->insert([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'kecamatan' => $request->kecamatan,
            'kodepos' => $request->kodepos,
            'foto_path' => $fileName
        ]);

        return redirect('/customer');
    }

    public function destroy($id)
    {
        // ambil data dulu (buat cek foto)
        $customer = DB::table('customer')->where('id', $id)->first();

        // hapus file kalau ada
        if ($customer && $customer->foto_path && file_exists(public_path('uploads/' . $customer->foto_path))) {
            unlink(public_path('uploads/' . $customer->foto_path));
        }

        // hapus dari database
        DB::table('customer')->where('id', $id)->delete();

        return redirect('/customer')->with('success', 'Data berhasil dihapus');
    }

    public function checkout(Request $request)
    {
        // generate ID pesanan
        $idpesanan = 'ORD' . rand(1000,9999);

        // contoh data (nanti bisa dari cart/session)
        $nama = $request->nama;
        $total = $request->total;

        // simpan ke database
        DB::table('pesanan')->insert([
            'idpesanan' => $idpesanan,
            'nama' => $nama,
            'total' => $total,
            'status' => 'Sudah Bayar'
        ]);

        // generate QR (isi = ID pesanan)
        $qrCode = QrCode::format('png')->size(200)->generate($idpesanan);
        $qrBase64 = base64_encode($qrCode);

        // kirim ke view
        $pesanan = (object)[
            'idpesanan' => $idpesanan,
            'nama' => $nama,
            'total' => $total
        ];

        return view('cust.success', compact('qrBase64', 'pesanan'));
    }

    public function updateStatus(Request $request)
    {
        DB::table('pesanan')
            ->where('idpesanan', $request->idpesanan)
            ->update([
                'status_bayar' => 1
            ]);

        return response()->json([
            'success' => true
        ]);
    }
}