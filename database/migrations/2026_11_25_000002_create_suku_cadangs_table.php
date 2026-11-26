<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suku_cadangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_part', 30)->unique();
            $table->string('nama_part', 100);
            $table->unsignedInteger('stok')->default(0);
            $table->unsignedBigInteger('harga');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suku_cadangs');
    }
};
