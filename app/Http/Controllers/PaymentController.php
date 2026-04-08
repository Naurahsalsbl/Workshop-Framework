<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    // Halaman pembayaran
    public function show($idpesanan)
    {
        $pesanan = DB::table('pesanan')->where('idpesanan', $idpesanan)->first();

        if (!$pesanan) {
            abort(404);
        }

        // Buat token Midtrans
        $params = [
            'transaction_details' => [
                'order_id'     => 'ORDER-' . $pesanan->idpesanan . '-' . time(),
                'gross_amount' => $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
            ],
            'enabled_payments' => ['bank_transfer', 'gopay'],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan token ke database
        DB::table('pesanan')->where('idpesanan', $idpesanan)->update([
            'midtrans_token' => $snapToken,
        ]);

        return view('customer.payment', compact('pesanan', 'snapToken'));
    }

    // Webhook notifikasi dari Midtrans
    public function handleNotification(Request $request)
    {
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId           = $notification->order_id; // ORDER-1-timestamp
        $fraudStatus       = $notification->fraud_status;

        // Ambil idpesanan dari order_id
        $parts     = explode('-', $orderId);
        $idpesanan = $parts[1];

        $pesanan = DB::table('pesanan')->where('idpesanan', $idpesanan)->first();

        if (!$pesanan) {
            return response()->json(['status' => 'not found'], 404);
        }

        if ($transactionStatus == 'capture' && $fraudStatus == 'accept') {
            DB::table('pesanan')->where('idpesanan', $idpesanan)->update(['status_bayar' => 1]);
        } elseif ($transactionStatus == 'settlement') {
            DB::table('pesanan')->where('idpesanan', $idpesanan)->update(['status_bayar' => 1]);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            DB::table('pesanan')->where('idpesanan', $idpesanan)->update(['status_bayar' => 2]);
        }

        return response()->json(['status' => 'ok']);
    }
}