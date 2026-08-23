<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas';

    protected $fillable = [
        'nomor_surat',
        'nama_survei',
        'format_surat',
        'jenis_mitra',
        'nama_mitra',
        'menimbang',
        'mengingat',
        'untuk',
        'wilayah_tugas',
        'waktu_tugas',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_surat',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'menimbang' => 'array',
        'mengingat' => 'array',
    ];
}