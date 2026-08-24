<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringSurvey extends Model
{
    // Mendaftarkan kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'nama_kegiatan',
        'bulan',
        'nama_pml',
        'nama_pcl',
        'satuan',
        'beban_banyak',
        'wilayah_tugas',
        'rate_honor',
        'honor_total',
    ];

    // Mendaftarkan hubungan agar sistem tahu siapa akun yang menginput data
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
