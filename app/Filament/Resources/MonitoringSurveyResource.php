<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringSurveyResource\Pages;
use App\Models\MonitoringSurvey;
use App\Models\SurveyActivity;
use App\Models\Pml;
use App\Models\Pcl; // Import Model Pcl
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MonitoringSurveyResource extends Resource
{
    protected static ?string $model = MonitoringSurvey::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'INPUT DATA';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Data Survei';

    protected static ?string $modelLabel = 'Data Survei';

    protected static ?string $pluralModelLabel = 'Data Survei';

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Informasi Kegiatan')
                    ->schema([

                        Select::make('nama_kegiatan')
                            ->label('Nama Kegiatan / Survei')
                            ->options(function () {
                                return SurveyActivity::where('status', 'Aktif')
                                    ->orderBy('nama_kegiatan')
                                    ->pluck('nama_kegiatan', 'nama_kegiatan');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('bulan')
                            ->label('Bulan Kegiatan')
                            ->options([
                                'Januari' => 'Januari',
                                'Februari' => 'Februari',
                                'Maret' => 'Maret',
                                'April' => 'April',
                                'Mei' => 'Mei',
                                'Juni' => 'Juni',
                                'Juli' => 'Juli',
                                'Agustus' => 'Agustus',
                                'September' => 'September',
                                'Oktober' => 'Oktober',
                                'November' => 'November',
                                'Desember' => 'Desember',
                            ])
                            ->required(),

                    ])
                    ->columns(2),

                Section::make('Informasi Mitra')
                    ->schema([

                        Select::make('nama_pml')
                            ->label('Nama PML')
                            ->options(function () {
                                return Pml::query()
                                    ->orderBy('nama_pml')
                                    ->pluck('nama_pml', 'nama_pml')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('nama_pcl')
                            ->label('Nama PCL')
                            ->options(function () {
                                return Pcl::all()->mapWithKeys(function ($pcl) {
                                    return [
                                        $pcl->nama_pcl => '<span class="text-gray-400 font-normal">' . $pcl->id_pcl . '</span> <span class="text-gray-400 font-normal">-</span> <span class="font-medium text-gray-900 dark:text-white">' . $pcl->nama_pcl . '</span>'
                                    ];
                                });
                            })
                            ->allowHtml()
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('satuan')
                            ->label('Satuan')
                            ->nullable(),

                        TextInput::make('beban_banyak')
                            ->label('Beban / Banyak')
                            ->numeric()
                            ->required()
                            ->live(),

                        TextInput::make('wilayah_tugas')
                            ->label('Wilayah Tugas')
                            ->nullable(),

                        TextInput::make('rate_honor')
                            ->label('Rate Honor yang Diterima (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live(),

                        TextInput::make('honor_total')
                            ->label('Honor Total')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($get) {

                                $beban = (float) ($get('beban_banyak') ?? 0);
                                $rate = (float) ($get('rate_honor') ?? 0);

                                return 'Rp ' . number_format(
                                    $beban * $rate,
                                    0,
                                    ',',
                                    '.'
                                );

                            }),

                    ])
                    ->columns(3),

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $orderBulan = "
            FIELD(
                bulan,
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
                'Desember'
            )
        ";

        return parent::getEloquentQuery()
            ->orderByRaw($orderBulan)
            ->orderBy('nama_pcl', 'asc')
            ->orderBy('nama_kegiatan', 'asc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->deferFilters(false)

            ->searchPlaceholder('Cari kegiatan, PML, atau PCL...')
            
            ->columns([

                Tables\Columns\TextColumn::make('no')
                    ->label('No.')
                    ->alignCenter()
                    ->rowIndex(),

                TextColumn::make('nama_kegiatan')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->badge()
                    ->color(function (string $state): string {

                        return match ($state) {

                            'Januari',
                            'Februari',
                            'Maret'
                                => 'warning',

                            'April',
                            'Mei',
                            'Juni'
                                => 'success',

                            'Juli',
                            'Agustus',
                            'September'
                                => 'info',

                            'Oktober',
                            'November',
                            'Desember'
                                => 'danger',

                            default => 'gray',
                        };

                    }),

                TextColumn::make('nama_pml')
                    ->label('Nama PML')
                    ->searchable(),

                TextColumn::make('nama_pcl')
                    ->label('Nama PCL')
                    ->searchable(),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('beban_banyak')
                    ->label('Beban / Banyak')
                    ->badge()
                    ->alignCenter()
                    ->color(function ($state) {

                        if ($state <= 5) {
                            return 'success';
                        }

                        if ($state <= 10) {
                            return 'warning';
                        }

                        return 'danger';
                    })
                    ->tooltip(function ($state){

                        if($state<=5){
                            return 'Beban Ringan';
                        }

                        if($state<=10){
                            return 'Beban Sedang';
                        }

                        return 'Beban Tinggi';

                    }),

                TextColumn::make('wilayah_tugas')
                    ->label('Wilayah Tugas')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('rate_honor')
                    ->label('Rate Honor')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('honor_total')
                    ->label('Honor Total')
                    ->money('IDR', locale: 'id')
                    ->weight('bold')
                    ->color('success'),

            ])
            
            ->filters([

                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                    ]),
                
                SelectFilter::make('nama_kegiatan')
                    ->label('Kegiatan')
                    ->options(function () {
                        return MonitoringSurvey::query()
                            ->distinct()
                            ->orderBy('nama_kegiatan')
                            ->pluck('nama_kegiatan', 'nama_kegiatan')
                            ->toArray();
                    }),

                SelectFilter::make('nama_pml')
                    ->label('PML')
                    ->searchable()
                    ->options(function () {
                        return MonitoringSurvey::query()
                            ->distinct()
                            ->orderBy('nama_pml')
                            ->pluck('nama_pml', 'nama_pml')
                            ->toArray();
                    }),

                SelectFilter::make('nama_pcl')
                    ->label('PCL')
                    ->searchable()
                    ->options(function () {
                        return MonitoringSurvey::query()
                            ->distinct()
                            ->orderBy('nama_pcl')
                            ->pluck('nama_pcl', 'nama_pcl')
                            ->toArray();
                    }),

            ])
            ->actions([

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])
            ->actionsColumnLabel('Aksi')
            
            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }

    public static function getPages(): array
    {
        return [

            'index' => Pages\ListMonitoringSurveys::route('/'),

            'create' => Pages\CreateMonitoringSurvey::route('/create'),

            'edit' => Pages\EditMonitoringSurvey::route('/{record}/edit'),

            'import' => Pages\ImportMonitoringSurveys::route('/import'),

        ];
    }
}