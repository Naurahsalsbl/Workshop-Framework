<?php

namespace App\Http\Controllers\Toko;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Http\Controllers\Controller;

class TokoController extends Controller
{
    // List toko
    public function index()
    {
        $toko = DB::table('toko')->get();
        return view('toko.index', compact('toko'));
    }

    // Form tambah toko
    public function create()
    {
        return view('toko.create');
    }

    // Simpan toko baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string',
        ]);

        $barcode = 'TK' . rand(1000,9999);

        DB::table('toko')->insert([
            'barcode'    => $barcode,
            'nama_toko'  => $request->nama_toko,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil ditambahkan!');
    }

    // Simpan titik awal (koordinat toko)
    public function simpanTitikAwal(Request $request, $id)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
        ]);

        DB::table('toko')->where('id', $id)->update([
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'accuracy'   => $request->accuracy,
            'updated_at' => now(),
        ]);

        $toko = DB::table('toko')->where('id', $id)->first();
        return response()->json(['success' => true, 'toko' => $toko]);
    }

    // Ambil data toko by barcode (untuk scan kunjungan)
    public function findByBarcode($barcode)
    {
        $toko = DB::table('toko')->where('barcode', $barcode)->first();
        if (!$toko) {
            return response()->json(['error' => 'Toko tidak ditemukan'], 404);
        }
        return response()->json($toko);
    }

    // Validasi kunjungan (tanpa simpan ke DB)
    public function validasiKunjungan(Request $request)
    {
        $request->validate([
            'toko_id'        => 'required|numeric',
            'lat_sales'      => 'required|numeric',
            'lng_sales'      => 'required|numeric',
            'accuracy_sales' => 'required|numeric',
        ]);

        $toko = DB::table('toko')
            ->where('id', $request->toko_id)
            ->first();

        if (!$toko) {
            return response()->json([
                'error' => 'Toko tidak ditemukan'
            ], 404);
        }

        if (
            is_null($toko->latitude) ||
            is_null($toko->longitude)
        ) {
            return response()->json([
                'error' => 'Titik awal toko belum diset!'
            ], 422);
        }

        $jarak = $this->haversine(
            $toko->latitude,
            $toko->longitude,
            $request->lat_sales,
            $request->lng_sales
        );

        $threshold = 300;

        $accuracyToko  = $toko->accuracy ?? 0;
        $accuracySales = $request->accuracy_sales ?? 0;

        $thresholdEfektif =
            $threshold +
            $accuracyToko +
            $accuracySales;

        $status =
            $jarak <= $thresholdEfektif
            ? 'DITERIMA'
            : 'DITOLAK';

        return response()->json([

            'status'            => $status,
            'nama_toko'         => $toko->nama_toko,

            'jarak_meter'       => round($jarak, 2),
            'threshold_efektif' => round($thresholdEfektif, 2),

            'accuracy_sales'    => round($accuracySales, 1),

            'latitude_sales'    => $request->lat_sales,
            'longitude_sales'   => $request->lng_sales,
        ]);
    }

    // Cetak barcode PDF
    public function cetakBarcode($id)
    {
        $toko = DB::table('toko')->where('id', $id)->first();
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode(
            $generator->getBarcode(
                $toko->barcode,
                $generator::TYPE_CODE_39,
                3,
                100
            )
        );

        return view('toko.barcode', compact('toko', 'barcode'));
    }

    // Formula Haversine
    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c    = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}