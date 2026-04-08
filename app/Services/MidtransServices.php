<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Buat transaksi Snap (popup/redirect)
     */
    public function createTransaction(array $params): object
    {
        return Snap::createTransaction($params);
    }

    /**
     * Handle notifikasi dari Midtrans (webhook)
     */
    public function getNotification(): Notification
    {
        return new Notification();
    }
}