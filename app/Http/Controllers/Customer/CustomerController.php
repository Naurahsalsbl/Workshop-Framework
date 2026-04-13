<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
}