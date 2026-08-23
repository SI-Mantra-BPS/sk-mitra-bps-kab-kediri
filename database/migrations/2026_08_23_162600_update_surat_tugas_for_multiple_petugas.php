<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            /*
             * Mengubah nama PCL menjadi nama mitra/petugas
             * agar dapat digunakan untuk PCL, PML, maupun Pegawai BPS.
             */
            $table->renameColumn('nama_pcl', 'nama_mitra');

            /*
             * Menambahkan jenis petugas.
             *
             * Data lama otomatis dianggap sebagai PCL
             * karena sebelumnya memang sistem hanya menggunakan PCL.
             */
            $table->string('jenis_mitra')
                ->default('PCL')
                ->after('nama_survei');

            $table->date('tanggal_mulai')
                ->nullable()
                ->after('wilayah_tugas');

            $table->date('tanggal_selesai')
                ->nullable()
                ->after('tanggal_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('surat_tugas', function (Blueprint $table) {
            $table->renameColumn('nama_mitra', 'nama_pcl');

            $table->dropColumn([
                'jenis_mitra',
                'tanggal_mulai',
                'tanggal_selesai',
            ]);
        });
    }
};