<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\SuratPerjanjianKerjaController;

Route::redirect('/', '/admin/login');

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');


// ==========================================
// ROUTE SURAT TUGAS
// ==========================================
Route::get(
    '/surat-tugas/semua/{namaSurvei}/pdf',
    [SuratTugasController::class, 'pdfSemua']
)->name('surat-tugas.semua.pdf');

Route::get(
    '/surat-tugas/{surat}/pdf',
    [SuratTugasController::class, 'pdf']
)->name('surat-tugas.pdf');


// ==========================================
// ROUTE SURAT PERJANJIAN KERJA (SPK)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Route cetak tunggal
    Route::get('/spk/cetak-pdf/{id?}', [SuratPerjanjianKerjaController::class, 'cetakPdf'])->name('spk.cetak-pdf');

    // PERBAIKAN: Mengarahkan ke method 'cetakSemuaPdf' di Controller
    Route::get('/spk/cetak-bulk-pdf', [SuratPerjanjianKerjaController::class, 'cetakSemuaPdf'])->name('spk.cetak-bulk-pdf');
});