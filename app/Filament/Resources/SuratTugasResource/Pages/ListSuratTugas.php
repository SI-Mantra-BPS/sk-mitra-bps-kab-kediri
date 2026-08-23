<?php

namespace App\Filament\Resources\SuratTugasResource\Pages;

use App\Filament\Resources\SuratTugasResource;
use App\Models\SuratTugas;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListSuratTugas extends ListRecords
{
    protected static string $resource = SuratTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
     
           Actions\CreateAction::make()
                ->label('Buat Surat')
                ->icon('heroicon-o-document-plus'),

            Actions\Action::make('cetakSemuaSurat')
                ->label('Cetak Surat (PDF)')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Semua Surat Tugas (PDF)')
                ->modalDescription(
                    'Pilih kegiatan untuk mencetak seluruh Surat Tugas pada kegiatan tersebut.'
                )
                ->modalSubmitActionLabel('Cetak PDF')
                ->modalCancelActionLabel('Batal')

                ->form([

                    Select::make('nama_survei')
                        ->label('Kegiatan')
                        ->options(function () {

                            return SuratTugas::query()
                                ->whereNotNull('nama_survei')
                                ->where('nama_survei', '!=', '')
                                ->select('nama_survei')
                                ->distinct()
                                ->orderBy('nama_survei')
                                ->pluck(
                                    'nama_survei',
                                    'nama_survei'
                                )
                                ->toArray();

                        })
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->placeholder('Pilih kegiatan'),

                ])

                ->action(function (array $data) {

                    return redirect()->route(
                        'surat-tugas.semua.pdf',
                        [
                            'namaSurvei' => $data['nama_survei'],
                        ]
                    );

                }),

        ];
    }
}