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
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            // Mengubah kolom survey_activity_id agar boleh kosong (nullable)
            $table->unsignedBigInteger('survey_activity_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_perjanjian_kerja', function (Blueprint $table) {
            $table->unsignedBigInteger('survey_activity_id')->nullable(false)->change();
        });
    }
};