<?php

namespace App\Http\Controllers\Kategori;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    // Proteksi route supaya hanya user login
    //public function __construct()
    //{
        //$this->middleware('auth');
    //}

    public function index()
    {
        // nanti bisa ambil data kategori dari database
        $kategori = DB::table('kategori')
            ->select('idkategori', 'nama_kategori')
            ->orderBy('idkategori', 'asc')
            ->get();
            
        return view('kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        DB::table('kategori')->insert([
            'nama_kategori' => $request->nama_kategori
        ]);
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = DB::table('kategori')->where('idkategori', $id)->first();
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        DB::table('kategori')->where('idkategori', $id)->update([
            'nama_kategori' => $request->nama_kategori
        ]);
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('kategori')->where('idkategori', $id)->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}

