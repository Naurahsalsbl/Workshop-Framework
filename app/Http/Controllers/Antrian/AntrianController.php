<?php

namespace App\Http\Controllers\Antrian;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\Antrian;

class AntrianController extends Controller
{
    public function __construct()
    {
        // Tutup session sebelum apapun di controller ini
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }
    
    // Daftar poli
    public const POLI_LIST = [
        'Umum', 'Poli Anak', 'Poli Jantung', 'Poli Mata',
        'Poli Gigi', 'Poli Kandungan', 'Poli Saraf', 'Poli Bedah',
        'Poli Penyakit Dalam', 'Laboratorium', 'Radiologi', 'Farmasi',
    ];

    // ─── Helper: sync DB → Cache ───────────────────────────────────
    private function syncCache()
    {
        $menunggu = DB::table('antrian')
            ->whereDate('created_at', today())
            ->where('status', 'menunggu')
            ->orderBy('nomor_antrian')
            ->get();

        $terlambat = DB::table('antrian')
            ->whereDate('created_at', today())
            ->where('status', 'terlambat')
            ->orderBy('nomor_antrian')
            ->get();

        $dipanggil = DB::table('antrian')
            ->whereDate('created_at', today())
            ->where('status', 'dipanggil')
            ->orderByDesc('dipanggil_at')
            ->first();

        $counts = DB::table('antrian')
            ->whereDate('created_at', today())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        Cache::put('antrian_data', [
            'menunggu'  => $menunggu,
            'terlambat' => $terlambat,
            'dipanggil' => $dipanggil,
            'counts'    => [
                'menunggu'  => $counts['menunggu']  ?? 0,
                'dipanggil' => $counts['dipanggil'] ?? 0,
                'terlambat' => $counts['terlambat'] ?? 0,
                'selesai'   => $counts['selesai']   ?? 0,
            ],
        ], now()->addHours(12));
    }

    // ─── Guest: form daftar antrian ────────────────────────────────
    public function guest()
    {
        return view('antrian.guest', ['poliList' => self::POLI_LIST]);
    }

    // ─── Guest: submit → dapat nomor antrian ──────────────────────
    public function daftar(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'poli' => 'required|string',
        ]);

        $lastNomor = DB::table('antrian')
            ->whereDate('created_at', today())
            ->max('nomor_antrian') ?? 0;

        $nomor = $lastNomor + 1;

        DB::table('antrian')->insert([
            'nomor_antrian' => $nomor,
            'nama'          => $request->nama,
            'poli'          => $request->poli,
            'status'        => 'menunggu',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->syncCache();

        return response()->json([
            'nomor' => $nomor,
            'nama'  => $request->nama,
            'poli'  => $request->poli,
        ]);
    }

    // ─── Tiket: halaman nomor antrian guest ───────────────────────
    public function tiket(Request $request)
    {
        return view('antrian.tiket', [
            'nomor' => $request->query('nomor'),
            'nama'  => $request->query('nama'),
            'poli'  => $request->query('poli'),
        ]);
    }

    // ─── Admin: dashboard ─────────────────────────────────────────
    public function admin()
    {
        return view('antrian.admin');
    }

    // ─── Admin: panggil nomor berikutnya ──────────────────────────
    public function panggil(Request $request)
    {
        $antrian = DB::table('antrian')
            ->whereDate('created_at', today())
            ->where('status', 'menunggu')
            ->orderBy('nomor_antrian')
            ->first();

        if (!$antrian) {
            return response()->json(['error' => 'Tidak ada antrian yang menunggu'], 404);
        }

        DB::table('antrian')->where('id', $antrian->id)->update([
            'status'       => 'dipanggil',
            'dipanggil_at' => now(),
            'updated_at'   => now(),
        ]);

        $this->syncCache();
        return response()->json(['success' => true]);
    }

    // ─── Admin: panggil antrian terlambat ─────────────────────────
    public function panggilTerlambat(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $antrian = DB::table('antrian')->where('id', $request->id)->first();
        if (!$antrian || $antrian->status !== 'terlambat') {
            return response()->json(['error' => 'Antrian tidak ditemukan'], 404);
        }

        DB::table('antrian')->where('id', $request->id)->update([
            'status'       => 'dipanggil',
            'dipanggil_at' => now(),
            'updated_at'   => now(),
        ]);

        $this->syncCache();
        return response()->json(['success' => true]);
    }

    // ─── Admin: tandai terlambat ───────────────────────────────────
    public function terlambat(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        DB::table('antrian')->where('id', $request->id)->update([
            'status'     => 'terlambat',
            'updated_at' => now(),
        ]);

        $this->syncCache();
        return response()->json(['success' => true]);
    }

    // ─── Admin: tandai selesai ────────────────────────────────────
    public function selesai(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        DB::table('antrian')->where('id', $request->id)->update([
            'status'     => 'selesai',
            'updated_at' => now(),
        ]);

        $this->syncCache();
        return response()->json(['success' => true]);
    }

    // ─── Admin: reset semua antrian hari ini ───────────────────────
    public function reset()
    {
        DB::table('antrian')->whereDate('created_at', today())->delete();
        Cache::forget('antrian_data');
        return response()->json(['success' => true]);
    }

    // ─── Papan antrian ─────────────────────────────────────────────
    public function papan()
    {
        return view('antrian.papan');
    }

    // ─── SSE Stream ────────────────────────────────────────────────
    public function stream()
    {
        // 1. Lepas lock session PHP agar halaman Admin & Papan bisa dibuka bersamaan
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        return response()->stream(function () {
            // Set waktu eksekusi PHP tidak terbatas untuk SSE
            set_time_limit(0);

            while (true) {
                // 2. Cek jika browser user ditutup, langsung matikan loop di server
                if (connection_aborted()) {
                    break;
                }

                // 3. Sinkronisasi data DB terbaru ke Cache
                $this->syncCache();

                // 4. Ambil data ter-update dari Cache
                $dataAntrian = Cache::get('antrian_data', [
                    'menunggu'  => [],
                    'terlambat' => [],
                    'dipanggil' => null,
                    'counts'    => ['menunggu' => 0, 'dipanggil' => 0, 'terlambat' => 0, 'selesai' => 0]
                ]);

                // 5. Kirim data sesuai format event 'queue-update' di JavaScript kamu
                echo "event: queue-update\n";
                echo "data: " . json_encode([
                    'dipanggil' => $dataAntrian['dipanggil'],
                    'menunggu'  => $dataAntrian['menunggu'],
                    'terlambat' => $dataAntrian['terlambat'],
                    'counts'    => $dataAntrian['counts']
                ]) . "\n\n";

                // 6. Paksa data langsung terkirim ke browser tanpa tertahan buffer server
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                // Jeda 1 detik agar tidak membebani performa CPU server
                sleep(1);
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store, must-revalidate',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}