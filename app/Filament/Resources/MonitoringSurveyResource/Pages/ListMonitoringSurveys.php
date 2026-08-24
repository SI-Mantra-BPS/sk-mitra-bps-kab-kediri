<?php

namespace App\Filament\Resources\MonitoringSurveyResource\Pages;

use App\Filament\Resources\MonitoringSurveyResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMonitoringSurveys extends ListRecords
{
    protected static string $resource = MonitoringSurveyResource::class;

    protected array $months = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Survei')
                ->icon('heroicon-o-document-plus')
                ->url(function () {

                    return static::getResource()::getUrl(
                        'create',
                        [
                            'activeTab' => $this->activeTab,
                        ]
                    );

                }),

            Actions\Action::make('importData')
                ->label('Import Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->url(
                    fn () => static::getResource()::getUrl('import')
                ),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'semua' => Tab::make('Semua Data'),
        ];

        foreach ($this->months as $month) {
            $tabs[strtolower($month)] = Tab::make($month)
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('bulan', $month)
                );
        }

        return $tabs;
    }

    public function mount(): void
    {
        parent::mount();

        if (
            session()->has('activeTab')
            && empty($this->activeTab)
        ) {

            $this->activeTab = session('activeTab');

            session()->forget('activeTab');
        }
    }
}