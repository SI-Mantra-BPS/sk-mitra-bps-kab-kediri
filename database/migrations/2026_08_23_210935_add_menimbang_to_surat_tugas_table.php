<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->json('menimbang')
                ->nullable()
                ->after('mengingat');
        });
    }

    public function down(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->dropColumn('menimbang');
        });
    }
};