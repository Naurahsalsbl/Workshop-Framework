<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        if (!session('vendor_id')) {
            return redirect()->route('vendor.login');
        }

        $menus = DB::table('menu')
            ->where('idvendor', session('vendor_id'))
            ->get();

        return view('vendor.menu.index', compact('menus'));
    }

    public function create()
    {
        if (!session('vendor_id')) {
            return redirect()->route('vendor.login');
        }

        return view('vendor.menu.create');
    }

    public function store(Request $request)
    {
        if (!session('vendor_id')) {
            return redirect()->route('vendor.login');
        }

        $request->validate([
            'nama_menu' => 'required',
            'harga'     => 'required|numeric',
        ]);

        // Handle upload gambar
        $path_gambar = null;
        if ($request->hasFile('path_gambar')) {
            $path_gambar = $request->file('path_gambar')->store('menu', 'public');
        }

        DB::table('menu')->insert([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $path_gambar,
            'idvendor'    => session('vendor_id'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        if (!session('vendor_id')) {
            return redirect()->route('vendor.login');
        }

        DB::table('menu')->where('idmenu', $id)->delete();

        return redirect()->route('vendor.menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}