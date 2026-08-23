<?php

namespace App\Filament\Resources\SuratTugasResource\Pages;

use App\Filament\Resources\SuratTugasResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSuratTugas extends EditRecord
{
    protected static string $resource = SuratTugasResource::class;

    /*
    |--------------------------------------------------------------------------
    | ISI DATA PENERIMA SAAT EDIT DIBUKA
    |--------------------------------------------------------------------------
    */
    protected function mutateFormDataBeforeFill(
        array $data
    ): array {

        $data['penerima'] = [

            [
                'nomor_surat' =>
                    $data['nomor_surat'] ?? '',

                'jenis_mitra' =>
                    $data['jenis_mitra'] ?? '',

                'nama_mitra' =>
                    $data['nama_mitra'] ?? '',

                'wilayah_tugas' =>
                    $data['wilayah_tugas'] ?? '',
            ],

        ];

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA PENERIMA
    |--------------------------------------------------------------------------
    */
    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        /*
         * Ambil penerima pertama.
         *
         * Pada halaman Edit kita hanya mengedit
         * satu Surat Tugas.
         */
        $penerima = $data['penerima'][0] ?? [];

        /*
         * Kembalikan data Repeater ke kolom
         * masing-masing di tabel surat_tugas.
         */

        $data['nomor_surat'] =
            $penerima['nomor_surat']
            ?? null;

        $data['jenis_mitra'] =
            $penerima['jenis_mitra']
            ?? null;

        $data['nama_mitra'] =
            $penerima['nama_mitra']
            ?? null;

        $data['wilayah_tugas'] =
            $penerima['wilayah_tugas']
            ?? null;

        /*
         * Repeater "penerima" bukan kolom database.
         */
        unset($data['penerima']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [

            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(
                            'Surat Tugas berhasil dihapus'
                        )
                ),

        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(
                'Surat Tugas berhasil diperbarui'
            );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}