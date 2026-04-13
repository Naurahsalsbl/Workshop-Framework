<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class VendorAuthController extends Controller
{
    // public function showLogin()
    // {
    //     return view('vendor.login');
    // }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $vendor = DB::table('vendor')
            ->where('username', $request->username)
            ->first();

        if (!$vendor || !Hash::check($request->password, $vendor->password)) {
            return back()->withErrors(['login' => 'Username atau password salah!']);
        }

        session([
            'vendor_id'   => $vendor->idvendor,
            'vendor_nama' => $vendor->nama_vendor,
        ]);

        return redirect()->route('vendor.dashboard');
    }

    public function logout()
    {
        session()->forget(['vendor_id', 'vendor_nama']);
        return redirect()->route('login');
    }

    public function masukSebagaiVendor($vendor_id)
    {
        $vendor = DB::table('vendor')->where('idvendor', $vendor_id)->first();

        if (!$vendor) abort(404);

        session([
            'vendor_id'   => $vendor->idvendor,
            'vendor_nama' => $vendor->nama_vendor,
        ]);

        return redirect()->route('vendor.dashboard');
    }
}