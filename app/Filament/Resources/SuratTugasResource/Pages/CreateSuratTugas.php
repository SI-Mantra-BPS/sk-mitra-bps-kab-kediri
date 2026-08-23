<?php

namespace App\Filament\Resources\SuratTugasResource\Pages;

use App\Filament\Resources\SuratTugasResource;
use App\Models\SuratTugas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSuratTugas extends CreateRecord
{
    protected static string $resource = SuratTugasResource::class;


    /**
     * ============================================================
     * BUAT SATU / BEBERAPA SURAT TUGAS
     * ============================================================
     */
    protected function handleRecordCreation(
        array $data
    ): Model {

        $penerima = $data['penerima'] ?? [];

        if (count($penerima) === 0) {

            throw new \RuntimeException(
                'Minimal harus ada satu penerima Surat Tugas.'
            );
        }

        $dataUmum = $data;

        unset(
            $dataUmum['penerima']
        );

        $createdRecords = DB::transaction(
            function () use (
                $penerima,
                $dataUmum
            ) {

                $records = [];

                foreach ($penerima as $item) {

                    $records[] = SuratTugas::create([

                        /*
                         * Data umum surat
                         */
                        ...$dataUmum,


                        /*
                         * Data khusus penerima
                         */
                        'nomor_surat' =>
                            $item['nomor_surat'],

                        'jenis_mitra' =>
                            $item['jenis_mitra'],

                        'nama_mitra' =>
                            $item['nama_mitra'],

                        'wilayah_tugas' =>
                            $item['wilayah_tugas']
                            ?? null,

                    ]);
                }

                return $records;
            }
        );

        return $createdRecords[0];
    }

    /**
     * ============================================================
     * NOTIFIKASI SETELAH CREATE
     * ============================================================
     */
    protected function getCreatedNotification(): ?Notification
    {
        $jumlah = count(
            $this->data['penerima'] ?? []
        );

        /*
         * Satu surat
         */
        if ($jumlah === 1) {

            return Notification::make()
                ->success()
                ->title(
                    'Surat Tugas berhasil dibuat'
                );
        }

        /*
         * Beberapa surat
         */
        return Notification::make()
            ->success()
            ->title(
                'Surat Tugas berhasil dibuat'
            )
            ->body(
                $jumlah .
                ' Surat Tugas berhasil dibuat.'
            );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}