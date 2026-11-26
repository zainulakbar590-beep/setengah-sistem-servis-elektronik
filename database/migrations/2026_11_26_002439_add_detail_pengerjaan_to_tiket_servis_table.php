<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            $table->text('diagnosa_kerusakan')->nullable()->after('keluhan');
            $table->text('tindakan_perbaikan')->nullable()->after('diagnosa_kerusakan');
        });
    }

    public function down(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            $table->dropColumn(['diagnosa_kerusakan', 'tindakan_perbaikan']);
        });
    }
};