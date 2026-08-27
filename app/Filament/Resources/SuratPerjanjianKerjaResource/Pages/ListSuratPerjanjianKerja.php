<?php

namespace App\Filament\Resources\SuratPerjanjianKerjaResource\Pages;

use App\Filament\Resources\SuratPerjanjianKerjaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPerjanjianKerja extends ListRecords
{
    protected static string $resource = SuratPerjanjianKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Surat'),

            Actions\Action::make('cetak_bulk_pdf')
                ->label('Cetak Surat (PDF)')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('spk.cetak-bulk-pdf', ['mode' => 'semua']))
                ->openUrlInNewTab(),
        ];
    }
}