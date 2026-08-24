<?php

namespace App\Imports;

use App\Models\MonitoringSurvey;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MonitoringSurveyImport implements ToCollection, WithHeadingRow
{
    public array $validRows = [];

    public array $invalidRows = [];

    public array $duplicateRows = [];

    /**
     * Header Excel berada pada baris ke-2.
     */
    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $this->validRows = [];
        $this->invalidRows = [];
        $this->duplicateRows = [];

        $months = [
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

        $excelKeys = [];

        foreach ($rows as $index => $row) {

            /*
             * Karena header berada di baris 2,
             * maka:
             *
             * $index = 0 -> Excel baris 3
             * $index = 1 -> Excel baris 4
             * dst.
             */
            $excelRow = $index + 3;

            /*
             * Baris 3 adalah baris contoh dari template.
             * Jangan masukkan sebagai data.
             */
            if ($excelRow === 3) {
                continue;
            }

            $namaKegiatan = trim(
                (string) ($row['nama_kegiatan'] ?? '')
            );

            $bulan = trim(
                (string) ($row['bulan'] ?? '')
            );

            $namaPml = trim(
                (string) ($row['nama_pml'] ?? '')
            );

            $namaPcl = trim(
                (string) ($row['nama_pcl'] ?? '')
            );

            $satuan = trim(
                (string) ($row['satuan'] ?? '')
            );

            $beban = $row['beban_banyak'] ?? null;

            $wilayah = trim(
                (string) ($row['wilayah_tugas'] ?? '')
            );

            $rate = $row['rate_honor'] ?? null;

            /*
             * Baris kosong dilewati.
             */
            if (
                $namaKegiatan === ''
                && $bulan === ''
                && $namaPml === ''
                && $namaPcl === ''
                && $satuan === ''
                && ($beban === null || $beban === '')
                && $wilayah === ''
                && ($rate === null || $rate === '')
            ) {
                continue;
            }

            $errors = [];

            /*
             * Validasi Nama Kegiatan.
             */
            if ($namaKegiatan === '') {
                $errors[] = 'Nama kegiatan wajib diisi.';
            }

            /*
             * Validasi Bulan.
             */
            if ($bulan === '') {

                $errors[] = 'Bulan wajib diisi.';

            } elseif (! in_array($bulan, $months, true)) {

                $errors[] = 'Bulan tidak valid.';
            }

            /*
             * Validasi PML.
             */
            if ($namaPml === '') {
                $errors[] = 'Nama PML wajib diisi.';
            }

            /*
             * Validasi PCL.
             */
            if ($namaPcl === '') {
                $errors[] = 'Nama PCL wajib diisi.';
            }

            /*
             * Validasi Satuan.
             */
            if ($satuan === '') {
                $errors[] = 'Satuan wajib diisi.';
            }

            /*
             * Validasi Beban / Banyak.
             */
            if (
                $beban === null
                || $beban === ''
                || ! is_numeric($beban)
                || (float) $beban < 0
            ) {
                $errors[] = 'Beban / Banyak harus berupa angka.';
            }

            /*
             * Validasi Rate Honor.
             */
            if (
                $rate === null
                || $rate === ''
                || ! is_numeric($rate)
                || (float) $rate < 0
            ) {
                $errors[] = 'Rate honor harus berupa angka.';
            }

            /*
             * Jika data tidak valid.
             */
            if (! empty($errors)) {

                $this->invalidRows[] = [
                    'row' => $excelRow,
                    'nama_kegiatan' => $namaKegiatan,
                    'bulan' => $bulan,
                    'nama_pml' => $namaPml,
                    'nama_pcl' => $namaPcl,
                    'satuan' => $satuan,
                    'beban_banyak' => $beban,
                    'wilayah_tugas' => $wilayah,
                    'rate_honor' => $rate,
                    'errors' => $errors,
                ];

                continue;
            }

            /*
             * Konversi angka.
             */
            $beban = (float) $beban;
            $rate = (float) $rate;

            /*
             * Honor Total dihitung otomatis.
             */
            $honorTotal = $beban * $rate;

            /*
             * Key untuk mendeteksi duplikasi
             * dalam file Excel.
             */
            $duplicateKey = implode('|', [
                strtolower($namaKegiatan),
                strtolower($bulan),
                strtolower($namaPml),
                strtolower($namaPcl),
                strtolower($satuan),
                $beban,
                strtolower($wilayah),
                $rate,
            ]);

            /*
             * Cek duplikat dalam file Excel.
             */
            if (isset($excelKeys[$duplicateKey])) {

                $this->duplicateRows[] = [
                    'row' => $excelRow,
                    'nama_kegiatan' => $namaKegiatan,
                    'bulan' => $bulan,
                    'nama_pml' => $namaPml,
                    'nama_pcl' => $namaPcl,
                    'reason' => 'Duplikat di dalam file Excel.',
                ];

                continue;
            }

            $excelKeys[$duplicateKey] = true;

            /*
             * Cek apakah data sudah ada di database.
             */
            $exists = MonitoringSurvey::query()
                ->where('nama_kegiatan', $namaKegiatan)
                ->where('bulan', $bulan)
                ->where('nama_pml', $namaPml)
                ->where('nama_pcl', $namaPcl)
                ->where('satuan', $satuan)
                ->where('beban_banyak', $beban)
                ->where('wilayah_tugas', $wilayah)
                ->where('rate_honor', $rate)
                ->exists();

            if ($exists) {

                $this->duplicateRows[] = [
                    'row' => $excelRow,
                    'nama_kegiatan' => $namaKegiatan,
                    'bulan' => $bulan,
                    'nama_pml' => $namaPml,
                    'nama_pcl' => $namaPcl,
                    'reason' => 'Data sudah ada di database.',
                ];

                continue;
            }

            /*
             * Data valid.
             */
            $this->validRows[] = [
                'nama_kegiatan' => $namaKegiatan,
                'bulan' => $bulan,
                'nama_pml' => $namaPml,
                'nama_pcl' => $namaPcl,
                'satuan' => $satuan,
                'beban_banyak' => $beban,
                'wilayah_tugas' => $wilayah,
                'rate_honor' => $rate,
                'honor_total' => $honorTotal,
            ];
        }
    }
}