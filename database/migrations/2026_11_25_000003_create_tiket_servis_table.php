<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_servis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('kategori_barang_id')->constrained('kategori_barangs')->cascadeOnDelete();
            $table->string('nama_barang', 100);
            $table->text('keluhan');
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'diambil'])->default('menunggu');
            $table->unsignedBigInteger('biaya_servis')->default(0);
            $table->date('tanggal_masuk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_servis');
    }
};