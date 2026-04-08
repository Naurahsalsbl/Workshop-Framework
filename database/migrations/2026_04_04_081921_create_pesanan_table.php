<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('idpesanan');
            $table->string('nama');
            $table->integer('total');
            $table->integer('metode_bayar')->nullable();
            $table->smallinteger('status_bayar')->default(0);
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_token')->nullable();
            $table->string('midtrans_va_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
