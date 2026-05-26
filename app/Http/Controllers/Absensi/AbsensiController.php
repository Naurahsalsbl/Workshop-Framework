<?php

namespace App\Http\Controllers\Absensi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AbsensiController extends Controller
{
    // ─── Halaman scan NFC ─────────────────────────────────────────
    public function scan()
    {
        $matakuliah = DB::table('mata_kuliah')->orderBy('nama')->get();
        return view('absensi.scan', compact('matakuliah'));
    }

    // ─── Proses scan NFC → catat absensi ──────────────────────────
    public function prosesScan(Request $request)
    {
        $request->validate([
            'nfc_serial'     => 'required|string',
            'matakuliah_id'  => 'required|integer|exists:mata_kuliah,id',
        ]);

        // Cari mahasiswa berdasarkan serial NFC
        $mahasiswa = DB::table('mahasiswa')
            ->where('nfc_serial', $request->nfc_serial)
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak terdaftar! Serial: ' . $request->nfc_serial,
            ], 404);
        }

        // Cek apakah sudah absen hari ini di matkul yang sama
        $sudahAbsen = DB::table('absensi')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('matakuliah_id', $request->matakuliah_id)
            ->whereDate('waktu_absen', today())
            ->first();

        if ($sudahAbsen) {
            return response()->json([
                'success'    => false,
                'message'    => $mahasiswa->nama . ' sudah absen hari ini!',
                'mahasiswa'  => $mahasiswa,
                'sudah_absen'=> true,
            ]);
        }

        // Tentukan status (terlambat jika setelah jam 08:00)
        $jamAbsen  = now();
        $batasJam  = now()->setTime(8, 0, 0);
        $status    = $jamAbsen->gt($batasJam) ? 'terlambat' : 'hadir';

        // Simpan absensi
        DB::table('absensi')->insert([
            'mahasiswa_id'  => $mahasiswa->id,
            'matakuliah_id' => $request->matakuliah_id,
            'nfc_serial'    => $request->nfc_serial,
            'waktu_absen'   => now(),
            'status'        => $status,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Absensi berhasil dicatat!',
            'mahasiswa' => $mahasiswa,
            'status'    => $status,
            'waktu'     => now()->format('H:i:s'),
        ]);
    }

    // ─── Halaman rekap absensi ────────────────────────────────────
    public function rekap(Request $request)
    {
        $matakuliah = DB::table('mata_kuliah')->orderBy('nama')->get();
        $matakuliah_id = $request->query('matakuliah_id');
        $tanggal       = $request->query('tanggal', today()->format('Y-m-d'));

        $absensi = [];
        if ($matakuliah_id) {
            $absensi = DB::table('absensi')
                ->join('mahasiswa', 'absensi.mahasiswa_id', '=', 'mahasiswa.id')
                ->join('mata_kuliah', 'absensi.matakuliah_id', '=', 'mata_kuliah.id')
                ->whereDate('absensi.waktu_absen', $tanggal)
                ->where('absensi.matakuliah_id', $matakuliah_id)
                ->select(
                    'mahasiswa.nim',
                    'mahasiswa.nama',
                    'mata_kuliah.nama as matkul',
                    'absensi.waktu_absen',
                    'absensi.status'
                )
                ->orderBy('absensi.waktu_absen')
                ->get();
        }

        return view('absensi.rekap', compact('matakuliah', 'absensi', 'matakuliah_id', 'tanggal'));
    }

    // ─── Halaman kelola mahasiswa ─────────────────────────────────
    public function mahasiswa()
    {
        $mahasiswa = DB::table('mahasiswa')->orderBy('nim')->get();
        return view('absensi.mahasiswa', compact('mahasiswa'));
    }

    // ─── Simpan mahasiswa baru ────────────────────────────────────
    public function storeMahasiswa(Request $request)
    {
        $request->validate([
            'nim'        => 'required|string|unique:mahasiswa,nim',
            'nama'       => 'required|string',
            'nfc_serial' => 'nullable|string|unique:mahasiswa,nfc_serial',
        ]);

        DB::table('mahasiswa')->insert([
            'nim'        => $request->nim,
            'nama'       => $request->nama,
            'nfc_serial' => $request->nfc_serial,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('absensi.mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan!');
    }

    // ─── Update NFC serial mahasiswa ──────────────────────────────
    public function updateNfc(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|integer',
            'nfc_serial'   => 'required|string',
        ]);

        // Cek apakah serial sudah dipakai mahasiswa lain
        $existing = DB::table('mahasiswa')
            ->where('nfc_serial', $request->nfc_serial)
            ->where('id', '!=', $request->mahasiswa_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Serial NFC sudah dipakai oleh ' . $existing->nama,
            ]);
        }

        DB::table('mahasiswa')
            ->where('id', $request->mahasiswa_id)
            ->update([
                'nfc_serial' => $request->nfc_serial,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Kartu NFC berhasil didaftarkan!']);
    }

    // ─── Kelola matakuliah ────────────────────────────────────────
    public function matakuliah()
    {
        $matakuliah = DB::table('mata_kuliah')->orderBy('nama')->get();
        return view('absensi.matakuliah', compact('matakuliah'));
    }

    public function storeMatakuliah(Request $request)
    {
        $request->validate([
            'kode'  => 'required|string|unique:mata_kuliah,kode',
            'nama'  => 'required|string',
            'dosen' => 'required|string',
        ]);

        DB::table('mata_kuliah')->insert([
            'kode'       => $request->kode,
            'nama'       => $request->nama,
            'dosen'      => $request->dosen,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('absensi.matakuliah')->with('success', 'Matakuliah berhasil ditambahkan!');
    }
}