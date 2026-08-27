<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            // Menambahkan kolom detail_kegiatan dengan tipe JSON / LongText
            $table->json('detail_kegiatan')->nullable()->after('tanggal_selesai_perjanjian');
        });
    }

    public function down(): void
    {
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            $table->dropColumn('detail_kegiatan');
        });
    }
};