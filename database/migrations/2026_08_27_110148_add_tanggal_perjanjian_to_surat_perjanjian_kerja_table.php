<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            $table->date('tanggal_mulai_perjanjian')->nullable()->after('alamat_pcl');
            $table->date('tanggal_selesai_perjanjian')->nullable()->after('tanggal_mulai_perjanjian');
        });
    }

    public function down(): void
    {
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_perjanjian', 'tanggal_selesai_perjanjian']);
        });
    }
};