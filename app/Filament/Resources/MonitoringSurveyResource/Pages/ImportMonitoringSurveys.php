<?php

namespace App\Filament\Resources\MonitoringSurveyResource\Pages;

use App\Exports\MonitoringSurveyTemplateExport;
use App\Filament\Resources\MonitoringSurveyResource;
use App\Imports\MonitoringSurveyImport;
use App\Models\MonitoringSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportMonitoringSurveys extends Page
{
    protected static string $resource =
        MonitoringSurveyResource::class;

    protected static string $view =
        'filament.pages.import-monitoring-surveys';

    protected static ?string $title = 'Import Data Survei';

    public ?array $data = [];

    public $file = null;

    public array $preview = [];

    public array $invalid = [];

    public array $duplicates = [];

    public bool $hasPreview = false;

    /**
     * Breadcrumb halaman.
     */
    public function getBreadcrumb(): string
    {
        return 'Import Data Survei';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\FileUpload::make('file')
                    ->label('File Excel Data Survei')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->disk('local')
                    ->directory('imports')
                    ->multiple(false)
                    ->required()
                    ->helperText(
                        'Gunakan file Excel sesuai template Data Survei.'
                    ),

            ])
            ->statePath('data');
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new MonitoringSurveyTemplateExport(),
            'template-data-survei.xlsx'
        );
    }

    public function previewData(): void
    {
        /*
        * Ambil state dari FileUpload.
        */
        $fileState = $this->data['file'] ?? null;

        /*
        * FileUpload dapat mengembalikan array
        * meskipun hanya satu file.
        */
        if (is_array($fileState)) {
            $fileState = array_values($fileState)[0] ?? null;
        }

        /*
        * Jika tidak ada file.
        */
        if (empty($fileState)) {

            Notification::make()
                ->title('File belum dipilih')
                ->body(
                    'Silakan pilih file Excel terlebih dahulu.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        * Tentukan path file.
        */
        $path = null;

        /*
        * KONDISI 1:
        * FileUpload masih berupa TemporaryUploadedFile.
        */
        if (
            $fileState instanceof
            \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
        ) {

            $path = $fileState->getRealPath();
        }

        /*
        * KONDISI 2:
        * File berupa UploadedFile.
        */
        elseif (
            $fileState instanceof
            \Illuminate\Http\UploadedFile
        ) {

            $path = $fileState->getRealPath();
        }

        /*
        * KONDISI 3:
        * FileUpload sudah menghasilkan path string.
        */
        elseif (is_string($fileState)) {

            $fileState = ltrim($fileState, '/');

            if (! Storage::disk('local')->exists($fileState)) {

                Notification::make()
                    ->title('File tidak ditemukan')
                    ->body(
                        'File upload tidak ditemukan di penyimpanan. Silakan upload kembali.'
                    )
                    ->danger()
                    ->send();

                return;
            }

            $path = Storage::disk('local')->path($fileState);
        }

        /*
        * Jika format state tidak dikenali.
        */
        else {

            Notification::make()
                ->title('Format file tidak valid')
                ->body(
                    'File Excel tidak dapat diproses. Silakan upload kembali file .xlsx atau .xls.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        * Pastikan path file tersedia.
        */
        if (! $path || ! is_string($path) || ! file_exists($path)) {

            Notification::make()
                ->title('File tidak ditemukan')
                ->body(
                    'File Excel tidak ditemukan. Silakan upload kembali.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        * Proses import / validasi Excel.
        */
        $import = new MonitoringSurveyImport();

        try {

            Excel::import(
                $import,
                $path
            );

        } catch (\Throwable $e) {

            report($e);

            Notification::make()
                ->title('Gagal membaca file Excel')
                ->body(
                    'Pastikan file menggunakan format Excel .xlsx atau .xls dan sesuai dengan template.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        * Simpan hasil validasi.
        */
        $this->preview = $import->validRows;

        $this->invalid = $import->invalidRows;

        $this->duplicates = $import->duplicateRows;

        $this->hasPreview = true;

        Notification::make()
            ->title('File berhasil diperiksa')
            ->body(
                count($this->preview) .
                ' data valid ditemukan.'
            )
            ->success()
            ->send();
    }

    public function importData(): void
    {
        if (! $this->hasPreview) {

            Notification::make()
                ->title('Preview belum dilakukan')
                ->body(
                    'Periksa file terlebih dahulu sebelum melakukan import.'
                )
                ->warning()
                ->send();

            return;
        }

        if (empty($this->preview)) {

            Notification::make()
                ->title('Tidak ada data valid')
                ->body(
                    'Tidak ada data yang dapat diimport.'
                )
                ->danger()
                ->send();

            return;
        }

        foreach ($this->preview as $row) {

            MonitoringSurvey::create([
                'user_id' => Auth::id(),
                'nama_kegiatan' => $row['nama_kegiatan'],
                'bulan' => $row['bulan'],
                'nama_pml' => $row['nama_pml'],
                'nama_pcl' => $row['nama_pcl'],
                'satuan' => $row['satuan'],
                'beban_banyak' => $row['beban_banyak'],
                'wilayah_tugas' => $row['wilayah_tugas'],
                'rate_honor' => $row['rate_honor'],
            ]);
        }

        Notification::make()
            ->title('Import berhasil')
            ->body(
                count($this->preview) .
                ' data berhasil diimport.'
            )
            ->success()
            ->send();

        $this->redirect(
            MonitoringSurveyResource::getUrl('index')
        );
    }

    public function cancelImport()
    {
        return $this->redirect(
            MonitoringSurveyResource::getUrl('index')
        );
    }
}