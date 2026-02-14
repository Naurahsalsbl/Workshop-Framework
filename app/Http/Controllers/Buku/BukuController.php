<?php

namespace App\Http\Controllers\Buku;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BukuController extends Controller
{
    // Proteksi route supaya hanya user login
    //public function __construct()
    //{
        //$this->middleware('auth');
    //}

    public function index()
    {
        // nanti bisa ambil data buku dari database
        $buku = DB::table('buku') 
                ->join('kategori', 'buku.idkategori', '=', 'kategori.idkategori')
                ->select('buku.*', 'kategori.nama_kategori')
                ->orderby('buku.idbuku', 'asc')
                ->get();
        return view('buku.index', compact('buku'));
    }

    public function create()
    {
        $kategori = DB::table('kategori')->get();
        return view('buku.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        DB::table('buku')->insert([
            'idkategori' => $request->idkategori,
            'kode' => $request->kode,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang
        ]);
        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = DB::table('kategori')->get();
        $buku = DB::table('buku')->where('idbuku', $id)->first();
        return view('buku.edit', compact('kategori', 'buku'));
    }

    public function update(Request $request, $id)
    {
        DB::table('buku')->where('idbuku', $id)->update([
            'idkategori' => $request->idkategori,
            'kode' => $request->kode,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang
        ]);
        return redirect()->route('buku.index')->with('success', 'Buku berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('buku')->where('idbuku', $id)->delete();
        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus!');
    }
}

