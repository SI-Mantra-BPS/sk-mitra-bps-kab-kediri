<?php

namespace App\Http\Controllers;

use App\Models\SuratPerjanjianKerja;
use App\Models\Pcl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
     * Cetak PDF untuk single (id) atau bulk terpilih (ids)
     */
    public function cetakPdf(Request $request)
    {
        $relations = $this->getBaseRelations();

        // 1. Cetak Single Data berdasarkan parameter ?id=
        if ($request->filled('id')) {
            $spk = SuratPerjanjianKerja::with($relations)->findOrFail($request->id);
            
            $spkList = collect([$spk]);
            $spkList = $this->processSpkData($spkList);
            $singleSpk = $spkList->first();

            $viewName = view()->exists('pdf.spk') ? 'pdf.spk' : 'spk';

            $pdf = Pdf::loadView($viewName, [
                'spk' => $singleSpk,
                'spkList' => $spkList,
            ])->setPaper('a4', 'portrait');

            // UBAH NAMA FILE SINGLE DI SINI
            $namaFile = 'SPK_' . ($singleSpk->nomor_spk ?? $singleSpk->id) . '.pdf';

            return $pdf->stream($namaFile);
        }

        // 2. Cetak Bulk Data berdasarkan parameter ?ids=1,2,3
        if ($request->filled('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            
            $spkList = SuratPerjanjianKerja::with($relations)
                ->whereIn('id', $ids)
                ->get();

            if ($spkList->isEmpty()) {
                abort(404, 'Data SPK terpilih tidak ditemukan.');
            }

            $spkList = $this->processSpkData($spkList);
            $viewName = view()->exists('pdf.spk') ? 'pdf.spk' : 'spk';

            $pdf = Pdf::loadView($viewName, [
                'spk' => $spkList->first(),
                'spkList' => $spkList,
            ])->setPaper('a4', 'portrait');

            // UBAH NAMA FILE BULK TERPILIH DI SINI
            $namaFile = 'SPK_Terpilih_' . date('Ymd_His') . '.pdf';

            return $pdf->stream($namaFile);
        }

        abort(404, 'Parameter ID atau IDs SPK tidak ditemukan.');
    }

    /**
     * Cetak semua PDF (Semua Data / Sesuai Filter)
     */
    public function cetakSemuaPdf(Request $request)
    {
        $relations = $this->getBaseRelations();
        $query = SuratPerjanjianKerja::with($relations);

        $mode = $request->query('mode', 'semua');
        $search = $request->query('search');

        // Jika user memilih opsi 'filter' dan ada kata kunci pencarian dari tabel Filament
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

        // =========================================================
        // UBAH NAMA FILE HASIL CETAK SEMUA / BULK PDF DI SINI:
        // =========================================================
        $namaFile = 'Surat_Perjanjian_Kerja_Semua_Data.pdf';

        return $pdf->stream($namaFile);
    }
}