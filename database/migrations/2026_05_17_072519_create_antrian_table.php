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
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_antrian');
            $table->string('nama');
            $table->string('poli');
            $table->enum('status', ['menunggu', 'dipanggil', 'terlambat', 'selesai'])->default('menunggu');
            $table->timestamp('dipanggil_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};
