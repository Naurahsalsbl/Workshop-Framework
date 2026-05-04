<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

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
                'order_id'     => 'ORDER-' . $pesanan->idpesanan . '-' . uniqid(),
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
            ],
            'enabled_payments' => ['bank_transfer', 'gopay'],

            'callbacks' => [
                'finish' => url('/payment/success/' . $pesanan->idpesanan)
            ]
        ];

        $transaction = Snap::createTransaction($params);
        $snapToken = Snap::getSnapToken($params);
        $redirectUrl = $transaction->redirect_url;


        // Simpan token ke database
        DB::table('pesanan')->where('idpesanan', $idpesanan)->update([
            'midtrans_token' => $snapToken,
        ]);

        return view('customer.payment', compact('pesanan', 'snapToken', 'redirectUrl'));
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

    public function detail($id)
    {
        $pesanan = DB::table('pesanan')->where('idpesanan', $id)->first();

        return view('customer.detail', compact('pesanan'));
    }

    public function success($idpesanan)
    {
        $pesanan = DB::table('pesanan')->where('idpesanan', $idpesanan)->first();

        if (!$pesanan) {
            abort(404);
        }

        $builder = new Builder(
            writer: new PngWriter(),
            data: url('/payment/detail/' . $pesanan->idpesanan),
            size: 200,
            margin: 5
        );

        $result = $builder->build();
        $qrBase64 = base64_encode($result->getString());

        return view('customer.success', compact('pesanan', 'qrBase64'));
    }

    public function updateStatus(Request $request)
    {
        DB::table('pesanan')
            ->where('idpesanan', $request->idpesanan)
            ->update([
                'status_bayar' => 1
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function showQR()
    {
        return view('payment.qr');
    }

    public function apiDetail($id)
    {
        $pesanan = DB::table('pesanan')->where('idpesanan', $id)->first();

        if (!$pesanan) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $menu = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.idmenu', '=', 'menu.idmenu')
            ->where('detail_pesanan.idpesanan', $id)
            ->select('menu.nama_menu as nama', 'detail_pesanan.jumlah as qty')
            ->get();

        return response()->json([
            'pesanan' => $pesanan,
            'menu' => $menu
        ]);
    }
}