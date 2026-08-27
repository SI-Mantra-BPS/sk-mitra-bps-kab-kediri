<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratPerjanjianKerjaResource\Pages;
use App\Models\SuratPerjanjianKerja;
use App\Models\Pcl;
use App\Models\MonitoringSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class SuratPerjanjianKerjaResource extends Resource
{
    protected static ?string $model = SuratPerjanjianKerja::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'DOKUMEN';
    
    protected static ?string $navigationLabel = 'Surat Perjanjian Kerja';
    protected static ?string $modelLabel = 'Surat Perjanjian Kerja';
    protected static ?string $pluralModelLabel = 'Surat Perjanjian Kerja';
    protected static ?string $breadcrumb = 'Surat Perjanjian Kerja';
    protected static ?string $slug = 'surat-perjanjian-kerja';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // CARD 1: INFORMASI UMUM SPK
                Forms\Components\Section::make('Informasi Surat Perjanjian Kerja')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_spk')
                            ->label('Nomor SPK')
                            ->placeholder('Contoh: PPIS-007.3/2910/KSA-JAGUNG/02/2026')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nama_ppk')
                            ->label('Nama Pihak Pertama (PPK)')
                            ->placeholder('Contoh: Hariyanti Ika Setyabudi, SE')
                            ->default('Hariyanti Ika Setyabudi, SE')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('tanggal_spk')
                            ->label('Tanggal SPK')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),
                    ])->columns(3),

                // CARD 2: PIHAK KEDUA (PCL)
                Forms\Components\Section::make('Informasi Pihak Kedua (PCL)')
                    ->schema([
                        Forms\Components\Select::make('pcl_id')
                            ->label('Nama PCL (Pihak Kedua)')
                            ->options(fn () => Pcl::pluck('nama_pcl', 'id_pcl')->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Nama PCL')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!$state) {
                                    return;
                                }

                                $pcl = Pcl::where('id_pcl', $state)->first();
                                
                                if ($pcl) {
                                    // Set Alamat PCL
                                    $alamat = $pcl->alamat ?? $pcl->alamat_pcl ?? '';
                                    $set('alamat_pcl', $alamat);

                                    // Fix Query Grouping agar data survei tidak tertukar
                                    $monitorings = MonitoringSurvey::query()
                                        ->where('nama_pcl', $pcl->nama_pcl)
                                        ->get();

                                    if ($monitorings->isNotEmpty()) {
                                        $repeaterItems = [];

                                        foreach ($monitorings as $item) {
                                            $vol = $item->beban_banyak ?? $item->volume ?? 1;
                                            $harga = $item->rate_honor ?? $item->harga_satuan ?? 0;
                                            $namaKegiatan = $item->nama_kegiatan ?? $item->uraian_tugas ?? 'Pencacahan Lapangan';
                                            
                                            $tglMulai = $item->tgl_mulai ?? null;
                                            $tglSelesai = $item->tgl_selesai ?? null;
                                            $jangkaWaktuText = self::calculateJangkaWaktu($tglMulai, $tglSelesai);

                                            $repeaterItems[] = [
                                                'uraian_tugas' => $namaKegiatan,
                                                'tgl_mulai_kegiatan' => $tglMulai,
                                                'tgl_selesai_kegiatan' => $tglSelesai,
                                                'jangka_waktu_text' => $jangkaWaktuText,
                                                'satuan' => $item->satuan ?? 'Dokumen',
                                                'volume' => $vol,
                                                'harga_satuan' => $harga,
                                                'nilai_perjanjian' => $vol * $harga,
                                                'beban_anggaran' => $item->beban_anggaran ?? '',
                                            ];
                                        }

                                        $set('detail_kegiatan', $repeaterItems);
                                    }
                                }
                            })
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('alamat_pcl')
                            ->label('Alamat Lengkap PCL')
                            ->placeholder('Contoh: RT 001 RW 002 Dusun Karangrejo Desa Karangrejo, Kecamatan Kandat, Kabupaten Kediri')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),

                        // TANGGAL PASAL 3
                        Forms\Components\DatePicker::make('tanggal_mulai_perjanjian')
                            ->label('Tanggal Mulai Perjanjian (Pasal 3)')
                            ->placeholder('Pilih Tanggal Mulai')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_selesai_perjanjian')
                            ->label('Tanggal Selesai Perjanjian (Pasal 3)')
                            ->placeholder('Pilih Tanggal Selesai')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),
                    ])->columns(2),

                // CARD 3: TABEL LAMPIRAN (REPEATER)
                Forms\Components\Section::make('Daftar Uraian Tugas & Kegiatan (Tabel Lampiran)')
                    ->description('Semua kegiatan PCL ditarik otomatis ke tabel di bawah ini. Anda dapat mengedit Uraian Tugas dan memilih Tanggal Kegiatan.')
                    ->schema([
                        Forms\Components\Repeater::make('detail_kegiatan')
                            ->schema([
                                Forms\Components\TextInput::make('uraian_tugas')
                                    ->label('Uraian Tugas')
                                    ->placeholder('Contoh: Pencacahan HK 2 dan 3')
                                    ->required()
                                    ->columnSpan(12),

                                Forms\Components\DatePicker::make('tgl_mulai_kegiatan')
                                    ->label('Tanggal Mulai')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateJangkaWaktuText($set, $get))
                                    ->columnSpan(3),

                                Forms\Components\DatePicker::make('tgl_selesai_kegiatan')
                                    ->label('Tanggal Selesai')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateJangkaWaktuText($set, $get))
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('satuan')
                                    ->label('Satuan')
                                    ->placeholder('Contoh: Dokumen')
                                    ->default('Dokumen')
                                    ->required()
                                    ->columnSpan(6),

                                Forms\Components\TextInput::make('beban_anggaran')
                                    ->label('Beban Anggaran')
                                    ->placeholder('Contoh: 2903.BMA.009.052.A.521213')
                                    ->required()
                                    ->columnSpan(12),

                                // HIDDEN FIELDS FOR PDF GENERATION
                                Forms\Components\Hidden::make('jangka_waktu_text')
                                    ->dehydrated(),
                                Forms\Components\Hidden::make('volume')
                                    ->default(1),
                                Forms\Components\Hidden::make('harga_satuan')
                                    ->default(0),
                                Forms\Components\Hidden::make('nilai_perjanjian')
                                    ->default(0),
                            ])
                            ->columns(12)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Baris Kegiatan Manual')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    // Helper terpisah untuk menghitung String Jangka Waktu
    public static function calculateJangkaWaktu(?string $start, ?string $end): string
    {
        if (!$start || !$end) {
            return '';
        }

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        if ($startDate->month === $endDate->month && $startDate->year === $endDate->year) {
            return $startDate->format('j') . ' sd ' . $endDate->isoFormat('D MMMM Y');
        }

        return $startDate->isoFormat('D MMMM Y') . ' sd ' . $endDate->isoFormat('D MMMM Y');
    }

    protected static function updateJangkaWaktuText(Set $set, Get $get): void
    {
        $start = $get('tgl_mulai_kegiatan');
        $end = $get('tgl_selesai_kegiatan');

        $set('jangka_waktu_text', self::calculateJangkaWaktu($start, $end));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_spk')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_ppk')
                    ->label('Nama PPK')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                // Menggunakan relasi pcl.nama_pcl (Disarankan menambahkan relasi pcl() di Model SuratPerjanjianKerja)
                // Atau penanganan fallback aman tanpa query terpisah berulang:
                Tables\Columns\TextColumn::make('pcl.nama_pcl')
                    ->label('Nama PCL (Pihak Kedua)')
                    ->searchable()
                    ->sortable()
                    ->default(fn (SuratPerjanjianKerja $record) => 
                        Pcl::where('id_pcl', $record->pcl_id)->value('nama_pcl') ?? '-'
                    ),

                Tables\Columns\TextColumn::make('total_kegiatan')
                    ->label('Jumlah Kegiatan')
                    ->getStateUsing(function (SuratPerjanjianKerja $record) {
                        $details = $record->detail_kegiatan;

                        if (is_string($details)) {
                            $details = json_decode($details, true);
                        }

                        return is_array($details) ? count($details) . ' Kegiatan' : '0 Kegiatan';
                    }),

                Tables\Columns\TextColumn::make('tanggal_spk')
                    ->label('Tanggal Surat')
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('pcl_id')
                    ->label('Filter PCL')
                    ->options(fn () => Pcl::pluck('nama_pcl', 'id_pcl')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    // PERBAIKAN: Kirim ID langsung sebagai parameter URL
                    ->url(fn (SuratPerjanjianKerja $record) => route('spk.cetak-pdf', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('cetak_banyak_pdf')
                        ->label('Cetak SPK Terpilih')
                        ->color('success')
                        ->icon('heroicon-o-printer')
                        // PERBAIKAN: Gunakan openUrlInNewTab via Livewire javascript agar bisa buka tab baru tanpa error redirect
                        ->action(function (Collection $records, $livewire) {
                            $ids = $records->pluck('id')->implode(',');
                            $url = route('spk.cetak-pdf-bulk', ['ids' => $ids]);
                            $livewire->js("window.open('{$url}', '_blank');");
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratPerjanjianKerja::route('/'),
            'create' => Pages\CreateSuratPerjanjianKerja::route('/create'),
            'edit' => Pages\EditSuratPerjanjianKerja::route('/{record}/edit'),
        ];
    }
}