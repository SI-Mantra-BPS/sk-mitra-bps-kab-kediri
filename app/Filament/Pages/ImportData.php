<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// Export
use App\Exports\MasterDataTemplateExport;

// Import
use App\Imports\MasterDataImport;

class ImportData extends Page
{
    use WithFileUploads;

    protected static string $view = 'filament.pages.import-data';

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $title = 'Import Master Data';

    protected static ?string $navigationGroup = 'MASTER DATA';

    protected static ?int $navigationSort = 5;

    /**
     * File Excel yang dipilih.
     */
    public mixed $excelFile = null;

    /**
     * Status apakah file sudah siap untuk preview.
     */
    public bool $hasFile = false;

    /**
     * Status apakah preview validasi sudah dibuka.
     */
    public bool $showPreview = false;

    /**
     * Counter validasi.
     */
    public int $totalKegiatanValid = 0;
    public int $totalPmlValid = 0;
    public int $totalPclValid = 0;

    /**
     * Total data gagal / dilewati.
     */
    public int $totalSkipped = 0;

    /**
     * Log preview validasi.
     */
    public array $previewValidLogs = [];

    public array $previewFailedLogs = [];

    /**
     * Statistik hasil import.
     */
    public int $totalKegiatanSuccess = 0;
    public int $totalPmlSuccess = 0;
    public int $totalPclSuccess = 0;

    /**
     * Digunakan untuk memaksa refresh input file
     * ketika pengguna memilih "Ganti File".
     */
    public int $fileInputKey = 0;

    /**
     * Download template Excel.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new MasterDataTemplateExport(),
            'Template_Import_Master_Data.xlsx'
        );
    }

    /**
     * Dipanggil ketika file Excel selesai dipilih.
     *
     * PENTING:
     * Pada tahap ini BELUM ada data yang disimpan.
     */
    public function updatedExcelFile(): void
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        $this->hasFile = true;

        $this->showPreview = false;

        $this->resetPreviewData();
    }

    /**
     * Membuka preview dan melakukan validasi file.
     *
     * Tidak menyimpan data ke database.
     */
    public function openPreview(): void
    {
        if (!$this->excelFile) {
            Notification::make()
                ->title('File Excel belum dipilih')
                ->body('Silakan pilih file Excel terlebih dahulu.')
                ->warning()
                ->send();

            return;
        }

        try {

            $import = new MasterDataImport(false);

            Excel::import(
                $import,
                $this->excelFile->getRealPath()
            );

            $logs = $import->getCombinedLogs();

            $this->previewValidLogs = $logs['valid'];
            $this->previewFailedLogs = $logs['failed'];

            $this->totalKegiatanValid =
                count($import->kegiatanSheet->validLogs);

            $this->totalPmlValid =
                count($import->pmlSheet->validLogs);

            $this->totalPclValid =
                count($import->pclSheet->validLogs);

            $this->totalSkipped =
                count($logs['failed']);

            $this->showPreview = true;

        } catch (\Exception $e) {

            Notification::make()
                ->title('Gagal Membaca File Excel')
                ->body('Terjadi kesalahan saat memvalidasi file: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /**
     * Mengganti file Excel.
     */
    public function replaceFile(): void
    {
        $this->excelFile = null;

        $this->hasFile = false;

        $this->showPreview = false;

        $this->resetPreviewData();

        $this->fileInputKey++;
    }

    /**
     * Membatalkan preview dan kembali ke file yang dipilih.
     */
    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    /**
     * Import final.
     *
     * BARU PADA METHOD INI DATA DISIMPAN KE DATABASE.
     */
    public function confirmImport(): void
    {
        if (!$this->excelFile) {
            Notification::make()
                ->title('File Excel tidak ditemukan')
                ->body('Silakan pilih file Excel terlebih dahulu.')
                ->warning()
                ->send();

            return;
        }

        try {

            DB::beginTransaction();

            /*
             * true = simpan ke database.
             */
            $import = new MasterDataImport(true);

            Excel::import(
                $import,
                $this->excelFile->getRealPath()
            );

            $this->totalKegiatanSuccess =
                count($import->kegiatanSheet->successLogs);

            $this->totalPmlSuccess =
                count($import->pmlSheet->successLogs);

            $this->totalPclSuccess =
                count($import->pclSheet->successLogs);

            $logs = $import->getCombinedLogs();

            DB::commit();

            $totalBerhasil =
                $this->totalKegiatanSuccess +
                $this->totalPmlSuccess +
                $this->totalPclSuccess;

            $totalGagal = count($logs['failed']);

            /*
             * Tutup preview.
             */
            $this->showPreview = false;

            /*
             * Reset file.
             */
            $this->excelFile = null;

            $this->hasFile = false;

            $this->resetPreviewData();

            $this->fileInputKey++;

            /*
             * Popup hasil akhir.
             */
            Notification::make()
                ->title('Import Data Berhasil!')
                ->body(
                    "Berhasil menyimpan {$totalBerhasil} data baru ke database."
                    . ($totalGagal > 0
                        ? " {$totalGagal} data dilewati karena duplikat atau tidak valid."
                        : '')
                )
                ->success()
                ->duration(7000)
                ->send();

        } catch (\Exception $e) {

            DB::rollBack();

            Notification::make()
                ->title('Gagal Mengimpor Data')
                ->body(
                    'Tidak ada data yang disimpan karena terjadi kesalahan: '
                    . $e->getMessage()
                )
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /**
     * Reset seluruh data preview.
     */
    protected function resetPreviewData(): void
    {
        $this->previewValidLogs = [];
        $this->previewFailedLogs = [];

        $this->totalKegiatanValid = 0;
        $this->totalPmlValid = 0;
        $this->totalPclValid = 0;
        $this->totalSkipped = 0;
    }
}