<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratTugasResource\Pages;
use App\Models\Pegawai;
use App\Models\Pml;
use App\Models\SuratTugas;
use App\Models\SurveyActivity;
use App\Models\MonitoringSurvey;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Surat Tugas';

    protected static ?string $modelLabel = 'Surat Tugas';

    protected static ?string $pluralModelLabel = 'Surat Tugas';

    protected static ?string $navigationGroup = 'DOKUMEN';

    protected static ?int $navigationSort = 1;

    // DEFAULT MENIMBANG
    protected static function defaultMenimbang(
        ?string $namaSurvei,
        string $format = 'format_1'
    ): array {

        $namaSurvei = trim((string) $namaSurvei);

        if ($namaSurvei === '') {
            $namaSurvei = 'kegiatan yang akan dilaksanakan';
        }

        // FORMAT 1 - Pegawai / Mitra BPS
        if ($format === 'format_1') {

            return [
                [
                    'poin' =>
                        'Bahwa untuk kelancaran pendataan ' .
                        $namaSurvei .
                        ', Kepala Badan Pusat Statistik Kabupaten Kediri perlu memberikan tugas/perintah kepada Pegawai/Mitra BPS dalam pelaksanaan kegiatan tersebut;',
                ],
            ];
        }

        // FORMAT 2 - Pegawai BPS
        return [
            [
                'poin' =>
                    'Bahwa dalam rangka kelancaran kegiatan ' .
                    $namaSurvei .
                    ', Kepala Badan Pusat Statistik Kabupaten Kediri perlu memberikan tugas/perintah kepada Pegawai BPS Kabupaten Kediri dalam pelaksanaan kegiatan tersebut.',
            ],
        ];
    }

    // DEFAULT MENGINGAT
    protected static function defaultMengingat(
        string $format = 'format_1'
    ): array {

        // FORMAT 1
        if ($format === 'format_1') {

            return [

                [
                    'poin' =>
                        'Undang-Undang Nomor 16 Tahun 1997 tentang Statistik;',
                ],

                [
                    'poin' =>
                        'Undang-Undang Nomor 17 Tahun 2025 tentang Anggaran Pendapatan dan Belanja Negara Tahun 2026;',
                ],

                [
                    'poin' =>
                        'Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;',
                ],

                [
                    'poin' =>
                        'Peraturan Presiden Nomor 86 Tahun 2007 tentang Badan Pusat Statistik;',
                ],

                [
                    'poin' =>
                        'Peraturan Badan Pusat Statistik Nomor 2 Tahun 2025 tentang Organisasi dan Tata Kerja Badan Pusat Statistik;',
                ],

                [
                    'poin' =>
                        'Peraturan Badan Pusat Statistik Nomor 3 Tahun 2025 tentang Perubahan atas Peraturan Badan Pusat Statistik Nomor 5 Tahun 2023 tentang Organisasi dan Tata Kerja Badan Pusat Statistik Provinsi dan Badan Pusat Statistik Kabupaten/Kota;',
                ],

                [
                    'poin' =>
                        'Peraturan Menteri Keuangan Nomor 107 Tahun 2024 tentang Perubahan Atas Peraturan Menteri Keuangan Nomor 62 Tahun 2023 tentang Perencanaan Anggaran, Pelaksanaan Anggaran Serta Akuntansi dan Pelaporan Keuangan;',
                ],

                [
                    'poin' =>
                        'Peraturan Menteri Keuangan Republik Indonesia No. 32 Tahun 2025 Tentang Standar Biaya Masukan (SBM) Tahun Anggaran 2026.',
                ],

            ];
        }

        // FORMAT 2
        return [

            [
                'poin' =>
                    'UU No. 16 Tahun 1997 tentang Statistik;',
            ],

            [
                'poin' =>
                    'Undang-Undang Nomor 6 Tahun 2014 tentang Desa;',
            ],

            [
                'poin' =>
                    'Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah sebagaimana diubah beberapa kali terakhir dengan Undang-Undang Nomor 9 Tahun 2015 tentang Perubahan Kedua atas Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah;',
            ],

            [
                'poin' =>
                    'Peraturan Pemerintah Nomor 51 Tahun 1999 tentang Penyelenggaraan Statistik;',
            ],

            [
                'poin' =>
                    'Peraturan Presiden Republik Indonesia Nomor 86 Tahun 2007 tentang Badan Pusat Statistik;',
            ],

            [
                'poin' =>
                    'Peraturan Badan Pusat Statistik Nomor 2 Tahun 2025 tentang Organisasi dan Tata Kerja Badan Pusat Statistik;',
            ],

        ];
    }

    // DEFAULT UNTUK
    protected static function defaultUntuk(
        ?string $namaSurvei,
        string $format = 'format_1'
    ): string {

        $namaSurvei = trim((string) $namaSurvei);

        if ($namaSurvei === '') {
            return 'Melaksanakan kegiatan yang akan dilaksanakan.';
        }

        // FORMAT 1
        if ($format === 'format_1') {

            return 'Melakukan Pemeriksaan dan Pengawasan Lapangan ' .
                $namaSurvei .
                '.';
        }

        // FORMAT 2
        return 'Mengajar Petugas Pengganti Lapangan ' .
            $namaSurvei .
            ' Tahun 2026';
    }

    // FORM
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // INFORMASI SURAT
                Section::make('Informasi Surat Tugas')
                    ->schema([

                        Select::make('format_surat')
                            ->label('Format Surat Tugas')
                            ->options([
                                'format_1' =>
                                    'Format 1 — Pegawai/Mitra',

                                'format_2' =>
                                    'Format 2 — Pegawai BPS',
                            ])
                            ->default('format_1')
                            ->native(false)
                            ->live()
                            ->required()
                            ->helperText(
                                'Pilih format surat yang akan digunakan.'
                            )
                            ->afterStateUpdated(
                                function (
                                    $state,
                                    Set $set,
                                    Get $get
                                ) {

                                    $format =
                                        $state ?: 'format_1';

                                    // Update Menimbang
                                    $set(
                                        'menimbang',
                                        self::defaultMenimbang(
                                            $get('nama_survei'),
                                            $format
                                        )
                                    );

                                    // Update Mengingat
                                    $set(
                                        'mengingat',
                                        self::defaultMengingat(
                                            $format
                                        )
                                    );

                                    // Update Untuk
                                    $set(
                                        'untuk',
                                        self::defaultUntuk(
                                            $get('nama_survei'),
                                            $format
                                        )
                                    );

                                    // Reset Penerima
                                    $set(
                                        'penerima',
                                        []
                                    );
                                }
                            ),


                        Select::make('nama_survei')
                            ->label('Nama Kegiatan / Survei')
                            ->options(function () {
                                return SurveyActivity::query()
                                    ->where('status', 'Aktif')
                                    ->orderBy('nama_kegiatan')
                                    ->pluck(
                                        'nama_kegiatan',
                                        'nama_kegiatan'
                                    )
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (
                                $state,
                                Set $set,
                                Get $get
                            ) {

                                // Reset data penerima
                                $set('penerima', []);

                                // Update Menimbang berdasarkan kegiatan
                                $set(
                                    'menimbang',
                                    self::defaultMenimbang(
                                        $state,
                                        $get('format_surat') ?: 'format_1'
                                    )
                                );

                                // Update Untuk berdasarkan kegiatan
                                $set(
                                    'untuk',
                                    self::defaultUntuk(
                                        $state,
                                        $get('format_surat') ?: 'format_1'
                                    )
                                );
                            })
                            ->required(),

                        DatePicker::make('tanggal_surat')
                            ->label('Tanggal Surat')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),

                    ])
                    ->columns(2),


                // MENIMBANG
                Section::make('Menimbang')
                    ->description(
                        'Pertimbangan otomatis disesuaikan dengan format surat. Anda tetap dapat menambah, mengubah, menghapus, atau mengurutkannya.'
                    )
                    ->headerActions([

                        FormAction::make('kelola_menimbang')
                            ->label('Tambah / Kelola Pertimbangan')
                            ->icon('heroicon-o-pencil-square')
                            ->color('warning')
                            ->modalHeading(
                                'Kelola Pertimbangan (Menimbang)'
                            )
                            ->modalSubmitActionLabel(
                                'Simpan Pertimbangan'
                            )
                            ->fillForm(
                                function (
                                    $record,
                                    Get $get
                                ) {

                                    $menimbang =
                                        $get('menimbang');

                                    if (
                                        is_array($menimbang)
                                        && count($menimbang) > 0
                                    ) {

                                        return [
                                            'menimbang_modal' =>
                                                $menimbang,
                                        ];
                                    }

                                    return [
                                        'menimbang_modal' =>
                                            self::defaultMenimbang(
                                                $get('nama_survei'),
                                                $get('format_surat')
                                                    ?: 'format_1'
                                            ),
                                    ];
                                }
                            )
                            ->form([

                                Repeater::make('menimbang_modal')
                                    ->hiddenLabel()
                                    ->itemLabel(
                                        function (
                                            array $state,
                                            Repeater $component
                                        ): ?string {

                                            $items =
                                                array_values(
                                                    $component->getState()
                                                    ?? []
                                                );

                                            $index =
                                                array_search(
                                                    $state,
                                                    $items,
                                                    true
                                                );

                                            return 'Pertimbangan ' .
                                                (
                                                    $index !== false
                                                        ? $index + 1
                                                        : ''
                                                );
                                        }
                                    )
                                    ->schema([

                                        Textarea::make('poin')
                                            ->hiddenLabel()
                                            ->rows(4)
                                            ->required()
                                            ->columnSpanFull(),

                                    ])
                                    ->addAction(
                                        fn (
                                            \Filament\Forms\Components\Actions\Action $action
                                        ) => $action
                                            ->label(
                                                'Tambah Pertimbangan Baru'
                                            )
                                            ->icon(
                                                'heroicon-o-plus'
                                            )
                                    )
                                    ->reorderable()
                                    ->deletable()
                                    ->collapsible(false)
                                    ->columnSpanFull(),

                            ])
                            ->action(
                                function (
                                    array $data,
                                    Set $set
                                ) {

                                    $set(
                                        'menimbang',
                                        $data['menimbang_modal']
                                            ?? []
                                    );
                                }
                            ),

                    ])
                    ->schema([

                        Hidden::make('menimbang')
                            ->default(
                                fn (Get $get) =>
                                    self::defaultMenimbang(
                                        $get('nama_survei'),
                                        $get('format_surat')
                                            ?: 'format_1'
                                    )
                            )
                            ->dehydrated(true),

                    ]),


                // MENGINGAT
                Section::make('Mengingat')
                    ->description(
                        'Dasar hukum otomatis disesuaikan dengan format surat. Anda tetap dapat menambah, mengubah, menghapus, atau mengurutkannya.'
                    )
                    ->headerActions([

                        FormAction::make('kelola_mengingat')
                            ->label('Tambah / Kelola Dasar Hukum')
                            ->icon('heroicon-o-pencil-square')
                            ->color('warning')
                            ->modalHeading(
                                'Kelola Dasar Hukum (Mengingat)'
                            )
                            ->modalSubmitActionLabel(
                                'Simpan Dasar Hukum'
                            )
                            ->fillForm(
                                function (
                                    $record,
                                    Get $get
                                ) {

                                    $mengingat =
                                        $get('mengingat');

                                    if (
                                        is_array($mengingat)
                                        && count($mengingat) > 0
                                    ) {

                                        return [
                                            'mengingat_modal' =>
                                                $mengingat,
                                        ];
                                    }

                                    return [
                                        'mengingat_modal' =>
                                            self::defaultMengingat(
                                                $get('format_surat')
                                                    ?: 'format_1'
                                            ),
                                    ];
                                }
                            )
                            ->form([

                                Repeater::make('mengingat_modal')
                                    ->hiddenLabel()
                                    ->itemLabel(
                                        function (
                                            array $state,
                                            Repeater $component
                                        ): ?string {

                                            $items =
                                                array_values(
                                                    $component->getState()
                                                    ?? []
                                                );

                                            $index =
                                                array_search(
                                                    $state,
                                                    $items,
                                                    true
                                                );

                                            return 'Dasar Hukum ' .
                                                (
                                                    $index !== false
                                                        ? $index + 1
                                                        : ''
                                                );
                                        }
                                    )
                                    ->schema([

                                        Textarea::make('poin')
                                            ->hiddenLabel()
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),

                                    ])
                                    ->addAction(
                                        fn (
                                            \Filament\Forms\Components\Actions\Action $action
                                        ) => $action
                                            ->label(
                                                'Tambah Dasar Hukum Baru'
                                            )
                                            ->icon(
                                                'heroicon-o-plus'
                                            )
                                    )
                                    ->reorderable()
                                    ->deletable()
                                    ->collapsible(false)
                                    ->columnSpanFull(),

                            ])
                            ->action(
                                function (
                                    array $data,
                                    Set $set
                                ) {

                                    $set(
                                        'mengingat',
                                        $data['mengingat_modal']
                                            ?? []
                                    );
                                }
                            ),

                    ])
                    ->schema([

                        Hidden::make('mengingat')
                            ->default(
                                fn (Get $get) =>
                                    self::defaultMengingat(
                                        $get('format_surat')
                                            ?: 'format_1'
                                    )
                            )
                            ->dehydrated(true),

                    ]),


                // PENERIMA SURAT
                Section::make('Penerima Surat')
                    ->description(
                        'Tambahkan satu atau beberapa penerima. Setiap penerima akan dibuat menjadi satu Surat Tugas.'
                    )
                    ->schema([

                        Repeater::make('penerima')
                            ->label('Daftar Penerima')
                            ->addActionLabel(
                                'Tambah Penerima'
                            )
                            ->reorderable()
                            ->collapsible(false)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->schema([


                                // NOMOR SURAT
                                TextInput::make('nomor_surat')
                                    ->label('Nomor Surat')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(
                                        'Contoh: B-099/3506/SS.340/2026'
                                    ),


                                // JENIS PETUGAS
                                Select::make('jenis_mitra')
                                    ->label('Jenis Petugas')
                                    ->options([
                                        'PCL' =>
                                            'PCL',

                                        'PML' =>
                                            'PML',

                                        'Pegawai BPS' =>
                                            'Pegawai BPS',
                                    ])
                                    ->native(false)
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(
                                        function (
                                            $state,
                                            Set $set
                                        ) {

                                            $set(
                                                'nama_mitra',
                                                null
                                            );

                                            $set(
                                                'wilayah_tugas',
                                                null
                                            );
                                        }
                                    ),


                                // NAMA PETUGAS
                                Select::make('nama_mitra')
                                    ->label('Nama Petugas')
                                    ->options(
                                        function (
                                            Get $get
                                        ) {

                                            $jenisMitra =
                                                $get(
                                                    'jenis_mitra'
                                                );

                                            $namaSurvei =
                                                $get(
                                                    '../../nama_survei'
                                                );


                                            // PCL
                                            if (
                                                $jenisMitra ===
                                                'PCL'
                                            ) {

                                                if (
                                                    ! $namaSurvei
                                                ) {
                                                    return [];
                                                }

                                                return MonitoringSurvey::query()
                                                    ->where(
                                                        'nama_kegiatan',
                                                        $namaSurvei
                                                    )
                                                    ->whereNotNull(
                                                        'nama_pcl'
                                                    )
                                                    ->where(
                                                        'nama_pcl',
                                                        '!=',
                                                        ''
                                                    )
                                                    ->select(
                                                        'nama_pcl'
                                                    )
                                                    ->distinct()
                                                    ->orderBy(
                                                        'nama_pcl'
                                                    )
                                                    ->pluck(
                                                        'nama_pcl',
                                                        'nama_pcl'
                                                    )
                                                    ->toArray();
                                            }


                                            // PML
                                            if (
                                                $jenisMitra ===
                                                'PML'
                                            ) {

                                                return Pml::query()
                                                    ->whereNotNull(
                                                        'nama_pml'
                                                    )
                                                    ->where(
                                                        'nama_pml',
                                                        '!=',
                                                        ''
                                                    )
                                                    ->orderBy(
                                                        'nama_pml'
                                                    )
                                                    ->pluck(
                                                        'nama_pml',
                                                        'nama_pml'
                                                    )
                                                    ->toArray();
                                            }


                                            // PEGAWAI BPS
                                            if (
                                                $jenisMitra ===
                                                'Pegawai BPS'
                                            ) {

                                                return Pegawai::query()
                                                    ->where(
                                                        'is_active',
                                                        true
                                                    )
                                                    ->whereNotNull(
                                                        'nama'
                                                    )
                                                    ->where(
                                                        'nama',
                                                        '!=',
                                                        ''
                                                    )
                                                    ->orderBy(
                                                        'nama'
                                                    )
                                                    ->pluck(
                                                        'nama',
                                                        'nama'
                                                    )
                                                    ->toArray();
                                            }

                                            return [];
                                        }
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->disabled(
                                        fn (
                                            Get $get
                                        ) =>
                                            ! $get(
                                                'jenis_mitra'
                                            )
                                    )
                                    ->afterStateUpdated(
                                        function (
                                            $state,
                                            Set $set,
                                            Get $get
                                        ) {

                                            // Hanya PCL yang otomatis mendapatkan wilayah
                                            if (
                                                $get(
                                                    'jenis_mitra'
                                                ) !== 'PCL'
                                                || ! $state
                                            ) {
                                                return;
                                            }

                                            $namaSurvei =
                                                $get(
                                                    '../../nama_survei'
                                                );

                                            if (
                                                ! $namaSurvei
                                            ) {
                                                return;
                                            }

                                            $wilayah =
                                                MonitoringSurvey::query()
                                                    ->where(
                                                        'nama_kegiatan',
                                                        $namaSurvei
                                                    )
                                                    ->where(
                                                        'nama_pcl',
                                                        $state
                                                    )
                                                    ->value(
                                                        'wilayah_tugas'
                                                    );

                                            if (
                                                $wilayah
                                            ) {

                                                $set(
                                                    'wilayah_tugas',
                                                    $wilayah
                                                );
                                            }
                                        }
                                    ),


                                // WILAYAH TUGAS
                                TextInput::make('wilayah_tugas')
                                    ->label('Wilayah Tugas')
                                    ->placeholder(
                                        'Contoh: Kecamatan Banyakan'
                                    )
                                    ->helperText(
                                        'Dapat diubah manual sesuai kebutuhan.'
                                    )
                                    ->visible(
                                        fn (
                                            Get $get
                                        ) =>
                                            $get(
                                                '../../format_surat'
                                            ) === 'format_1'
                                    )
                                    ->columnSpanFull(),

                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                    ]),


                // DETAIL PENUGASAN
                Section::make('Detail Penugasan')
                    ->schema([

                        Textarea::make('untuk')
                            ->label('Untuk')
                            ->required()
                            ->rows(3)
                            ->placeholder(
                                'Contoh: Untuk melaksanakan kegiatan IBS Triwulan 4 di wilayah kerja yang telah ditentukan.'
                            )
                            ->columnSpanFull(),

                    ]),


                // JANGKA WAKTU
                Section::make('Jangka Waktu')
                    ->description(
                        'Tentukan periode pelaksanaan tugas.'
                    )
                    ->schema([

                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),


                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required()
                            ->afterOrEqual(
                                'tanggal_mulai'
                            )
                            ->validationMessages([
                                'after_or_equal' =>
                                    'Tanggal selesai harus sama atau setelah tanggal mulai.',
                            ]),

                    ])
                    ->columns(2),

            ]);
    }


    // TABLE
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->alignCenter(),


                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('format_surat')
                    ->label('Format')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'format_1' => 'Format 1',
                        'format_2' => 'Format 2',
                        default => '-',
                    })
                    ->badge()
                    ->color('info'),


                TextColumn::make('nama_survei')
                    ->label('Nama Survei')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('jenis_mitra')
                    ->label('Jenis Petugas')
                    ->badge()
                    ->color('warning'),


                TextColumn::make('nama_mitra')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('wilayah_tugas')
                    ->label('Wilayah Tugas')
                    ->limit(40)
                    ->tooltip(
                        fn ($record) =>
                            $record->wilayah_tugas
                    )
                    ->placeholder('-'),


                TextColumn::make('tanggal_mulai')
                    ->label('Jangka Waktu')
                    ->sortable()
                    ->formatStateUsing(
                        function (
                            $state,
                            $record
                        ) {

                            if (! $state) {
                                return '-';
                            }

                            $mulai =
                                \Carbon\Carbon::parse(
                                    $state
                                )->format('d F Y');

                            if (
                                ! $record->tanggal_selesai
                            ) {
                                return $mulai;
                            }

                            $selesai =
                                \Carbon\Carbon::parse(
                                    $record->tanggal_selesai
                                )->format('d F Y');

                            return $mulai .
                                ' s.d. ' .
                                $selesai;
                        }
                    ),


                TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d F Y')
                    ->sortable(),

            ])


            ->filters([

                // KEGIATAN
                SelectFilter::make('nama_survei')
                    ->label('Kegiatan')
                    ->options(
                        fn () =>
                            SuratTugas::query()
                                ->whereNotNull('nama_survei')
                                ->where('nama_survei', '!=', '')
                                ->select('nama_survei')
                                ->distinct()
                                ->orderBy('nama_survei')
                                ->pluck(
                                    'nama_survei',
                                    'nama_survei'
                                )
                                ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),


                // TAHUN
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(
                        fn () =>
                            SuratTugas::query()
                                ->whereNotNull('tanggal_surat')
                                ->selectRaw('YEAR(tanggal_surat) as tahun')
                                ->distinct()
                                ->orderByDesc('tahun')
                                ->pluck(
                                    'tahun',
                                    'tahun'
                                )
                                ->toArray()
                    )
                    ->native(false)
                    ->query(
                        function ($query, array $data) {

                            if (! empty($data['value'])) {

                                $query->whereYear(
                                    'tanggal_surat',
                                    $data['value']
                                );
                            }
                        }
                    ),


                // JENIS PETUGAS
                SelectFilter::make('jenis_mitra')
                    ->label('Jenis Petugas')
                    ->options([
                        'PCL' => 'PCL',
                        'PML' => 'PML',
                        'Pegawai BPS' => 'Pegawai BPS',
                    ])
                    ->native(false),


                // NAMA PETUGAS
                SelectFilter::make('nama_mitra')
                    ->label('Nama Petugas')
                    ->options(
                        fn () =>
                            SuratTugas::query()
                                ->whereNotNull('nama_mitra')
                                ->where('nama_mitra', '!=', '')
                                ->select('nama_mitra')
                                ->distinct()
                                ->orderBy('nama_mitra')
                                ->pluck(
                                    'nama_mitra',
                                    'nama_mitra'
                                )
                                ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

            ])
            ->filtersLayout(
                FiltersLayout::AboveContent
            )
            ->filtersFormColumns(4)

            // ACTIONS
            ->actions([

                // PREVIEW
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(
                        fn ($record) =>
                            'Preview Surat Tugas - ' . $record->nomor_surat
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('7xl')
                    ->modalContent(
                        fn ($record) =>
                            view(
                                'filament.pages.surat-tugas-preview',
                                [
                                    'surat' => $record,
                                ]
                            )
                    ),

                // CETAK PDF
                Tables\Actions\Action::make('pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(
                        fn ($record) =>
                            route(
                                'surat-tugas.pdf',
                                $record
                            )
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            ->actionsColumnLabel('Aksi')


            // BULK ACTION
            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }


    // RELATIONS
    public static function getRelations(): array
    {
        return [];
    }


    // PAGES
    public static function getPages(): array
    {
        return [

            'index' =>
                Pages\ListSuratTugas::route('/'),

            'create' =>
                Pages\CreateSuratTugas::route('/create'),

            'edit' =>
                Pages\EditSuratTugas::route(
                    '/{record}/edit'
                ),

        ];
    }
}