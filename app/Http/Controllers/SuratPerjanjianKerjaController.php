<?php

namespace App\Http\Controllers;

use App\Models\SuratPerjanjianKerja;
use App\Models\Pcl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPerjanjianKerjaController extends Controller
{
    /**
     * Array relasi dasar yang aman untuk di-eager load.
     */
    private function getBaseRelations(): array
    {
        $relations = [];
        $spkModel = new SuratPerjanjianKerja();

        if (method_exists($spkModel, 'pcl')) {
            $relations[] = 'pcl';
        }

        return $relations;
    }

    /**
     * Helper privat untuk melengkapi data SPK dan parse array detail_kegiatan
     * 
     * @param Collection $spkCollection
     * @return Collection
     */
    private function processSpkData(Collection $spkCollection): Collection
    {
        return $spkCollection->map(function ($spk) {
            // 1. Ambil Nama & Alamat PCL
            $pcl = $spk->pcl ?? Pcl::where('id_pcl', $spk->pcl_id)->first();
            $spk->nama_pcl_formatted = $pcl?->nama_pcl ?? ($spk->nama_pcl ?? '-');
            $spk->alamat_pcl_formatted = $spk->alamat_pcl ?? ($pcl?->alamat ?? '-');

            // 2. Format / Decode detail_kegiatan dari Repeater Filament
            $details = is_array($spk->detail_kegiatan)
                ? $spk->detail_kegiatan
                : json_decode($spk->detail_kegiatan ?? '[]', true);

            // 3. Hitung total nilai perjanjian untuk tabel lampiran
            $totalNilai = collect($details)->sum(function ($item) {
                $vol = (float) ($item['volume'] ?? 1);
                $harga = (float) ($item['harga_satuan'] ?? 0);
                return (float) ($item['nilai_perjanjian'] ?? ($vol * $harga));
            });

            $spk->parsed_detail_kegiatan = $details;
            $spk->total_nilai_perjanjian = $totalNilai;

            return $spk;
        });
    }

    /**
     * Cetak PDF Single/Tunggal per Baris
     * Mengambil ID dari Path Route: /spk/cetak-pdf/{id}
     */
    public function cetakPdf(Request $request, $id = null)
    {
        // Fallback jika ID dikirim melalui query parameter ?id=1
        $targetId = $id ?? $request->query('id');

        if (!$targetId) {
            abort(404, 'ID Surat Perjanjian Kerja tidak ditemukan.');
        }

        $relations = $this->getBaseRelations();
        $spk = SuratPerjanjianKerja::with($relations)->findOrFail($targetId);
        
        $spkList = collect([$spk]);
        $spkList = $this->processSpkData($spkList);
        $singleSpk = $spkList->first();

        $viewName = view()->exists('pdf.spk') ? 'pdf.spk' : 'spk';

        $pdf = Pdf::loadView($viewName, [
            'spk' => $singleSpk,
            'spkList' => $spkList,
        ])->setPaper('a4', 'portrait');

        // Bersihkan nomor SPK dari garis miring (/) agar tidak merusak nama file saat download
        $safeNomorSpk = Str::slug($singleSpk->nomor_spk ?? $singleSpk->id, '_');
        $namaFile = 'SPK_' . $safeNomorSpk . '.pdf';

        return $pdf->stream($namaFile);
    }

    /**
     * Cetak PDF Terpilih (Bulk Checkbox) atau Semua Data
     * Mengambil parameter query ?ids=1,2,3 dari route: /spk/cetak-bulk-pdf
     */
    public function cetakSemuaPdf(Request $request)
    {
        $relations = $this->getBaseRelations();
        $query = SuratPerjanjianKerja::with($relations);

        // 1. Jika mencetak data terpilih via checkbox (Bulk Action)
        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $query->whereIn('id', array_filter($ids));
        }

        // 2. Jika mencetak dengan filter pencarian dari Filament
        $mode = $request->query('mode', 'semua');
        $search = $request->query('search');

        if ($mode === 'filter' && !empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_spk', 'like', "%{$search}%")
                  ->orWhere('nama_ppk', 'like', "%{$search}%")
                  ->orWhereHas('pcl', function ($pclQuery) use ($search) {
                      $pclQuery->where('nama_pcl', 'like', "%{$search}%");
                  });
            });
        }

        $spkList = $query->latest()->get();

        if ($spkList->isEmpty()) {
            return back()->with('error', 'Tidak ada data Surat Perjanjian Kerja yang dapat dicetak.');
        }

        $spkList = $this->processSpkData($spkList);
        $viewName = view()->exists('pdf.spk') ? 'pdf.spk' : 'spk';

        $pdf = Pdf::loadView($viewName, [
            'spk' => $spkList->first(),
            'spkList' => $spkList,
        ])->setPaper('a4', 'portrait');

        $namaFile = $request->filled('ids') 
            ? 'SPK_Terpilih_' . date('Ymd_His') . '.pdf'
            : 'Surat_Perjanjian_Kerja_Semua_Data.pdf';

        return $pdf->stream($namaFile);
    }
}