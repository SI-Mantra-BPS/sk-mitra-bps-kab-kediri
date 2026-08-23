<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->string('format_surat', 20)
                ->default('format_1')
                ->after('nama_survei');
        });
    }

    public function down(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->dropColumn('format_surat');
        });
    }
};