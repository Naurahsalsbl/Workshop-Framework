<?php

namespace App\Http\Controllers\Barang;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barang = DB::table('barang')
                    ->orderBy('id_barang','asc')
                    ->get();

        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        DB::table('barang')->insert([
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga
        ]);

        return redirect()->route('barang.index')
                ->with('success','Barang berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $barang = DB::table('barang')
                    ->where('id_barang',$id)
                    ->first();

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        DB::table('barang')
            ->where('id_barang',$id)
            ->update([
                'nama_barang' => $request->nama_barang,
                'harga' => $request->harga
            ]);

        return redirect()->route('barang.index')
                ->with('success','Barang berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('barang')
            ->where('id_barang',$id)
            ->delete();

        return redirect()->route('barang.index')
                ->with('success','Barang berhasil dihapus!');
    }

    public function cetakLabel(Request $request)
    {
        if (!$request->pilih) {
        return redirect()->back()
               ->with('success','Pilih minimal 1 barang!');
        }

        $x = $request->x;
        $y = $request->y;
        $pilih = $request->pilih;

        // ambil data barang yang dipilih
        $barang = DB::table('barang')
                    ->whereIn('id_barang', $request->pilih)
                    ->get();

        $pdf = Pdf::loadView('barang.cetak', compact('barang','x','y'))
              ->setPaper('a4', 'portrait');

        return $pdf->stream('label-barang.pdf');

        //return view('barang.cetak', compact('barang','x','y'));
    }
}