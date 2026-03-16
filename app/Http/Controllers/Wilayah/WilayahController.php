<?php

namespace App\Http\Controllers\Wilayah;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WilayahController extends Controller
{
    /**
     * Tampilkan halaman dropdown wilayah
     * Route: GET /wilayah
     */
    public function index()
    {
        // Ambil semua provinsi dari database
        $provinsi = DB::table('reg_provinces')->orderBy('name')->get();

        return view('wilayah.index', compact('provinsi'));
    }

    /**
     * Ambil daftar kota berdasarkan id provinsi
     * Route: GET /wilayah/kota?province_id=xx
     */
    public function getKota(Request $request)
    {
        $province_id = $request->get('province_id');

        if (!$province_id) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Province ID tidak boleh kosong',
                'data'    => []
            ], 400);
        }

        $kota = DB::table('reg_regencies')
            ->where('province_id', $province_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kota berhasil diambil',
            'data'    => $kota
        ]);
    }

    /**
     * Ambil daftar kecamatan berdasarkan id kota/regency
     * Route: GET /wilayah/kecamatan?regency_id=xx
     */
    public function getKecamatan(Request $request)
    {
        $regency_id = $request->get('regency_id');

        if (!$regency_id) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'Regency ID tidak boleh kosong',
                'data'    => []
            ], 400);
        }

        $kecamatan = DB::table('reg_districts')
            ->where('regency_id', $regency_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kecamatan berhasil diambil',
            'data'    => $kecamatan
        ]);
    }

    /**
     * Ambil daftar kelurahan berdasarkan id kecamatan/district
     * Route: GET /wilayah/kelurahan?district_id=xx
     */
    public function getKelurahan(Request $request)
    {
        $district_id = $request->get('district_id');

        if (!$district_id) {
            return response()->json([
                'status'  => 'error',
                'code'    => 400,
                'message' => 'District ID tidak boleh kosong',
                'data'    => []
            ], 400);
        }

        $kelurahan = DB::table('reg_villages')
            ->where('district_id', $district_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data kelurahan berhasil diambil',
            'data'    => $kelurahan
        ]);
    }
}