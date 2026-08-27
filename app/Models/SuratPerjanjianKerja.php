<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPerjanjianKerja extends Model
{
    use HasFactory;

    protected $table = 'surat_perjanjian_kerja';

    protected $fillable = [
        'nomor_spk',
        'nama_ppk',
        'tanggal_spk',
        'pcl_id',
        'alamat_pcl',
        'tanggal_mulai_perjanjian',
        'tanggal_selesai_perjanjian',
        'detail_kegiatan', // Field Repeater JSON
        'survey_activity_id',
        'beban_anggaran',
        'uraian_tugas',
        'satuan',
    ];

    /**
     * KUNCI UTAMA FIX:
     * Casting 'detail_kegiatan' ke array agar Filament dapat membaca & menyimpan 
     * struktur Repeater JSON secara otomatis tanpa error.
     */
    protected $casts = [
        'tanggal_spk' => 'date',
        'tanggal_mulai_perjanjian' => 'date',
        'tanggal_selesai_perjanjian' => 'date',
        'detail_kegiatan' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DASAR
    |--------------------------------------------------------------------------
    */

    public function surveyActivity(): BelongsTo
    {
        return $this->belongsTo(SurveyActivity::class, 'survey_activity_id');
    }

    public function pcl(): BelongsTo
    {
        return $this->belongsTo(Pcl::class, 'pcl_id', 'id_pcl')
            ->withDefault([
                'nama_pcl' => '-',
                'alamat' => '-',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR FALLBACK (Dukungan Tampilan Filament & PDF)
    |--------------------------------------------------------------------------
    */

    protected function pclData(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->pcl_id)) {
                    $pcl = Pcl::where('id_pcl', $this->pcl_id)->first();
                    if ($pcl) return $pcl;
                }

                return $this->pcl;
            }
        );
    }

    protected function namaPclDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->pcl && $this->pcl->nama_pcl !== '-') {
                    return $this->pcl->nama_pcl;
                }
                return $this->pcl_data?->nama_pcl ?? '-';
            }
        );
    }

    protected function alamatLengkapPcl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!empty($this->alamat_pcl)) return $this->alamat_pcl;
                if ($this->pcl && $this->pcl->alamat !== '-') return $this->pcl->alamat;

                return $this->pcl_data?->alamat ?? '-';
            }
        );
    }
}