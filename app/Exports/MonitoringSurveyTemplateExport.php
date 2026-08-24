<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringSurveyTemplateExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function headings(): array
    {
        return [
            [
                '* Keterangan: Isi data survei sesuai kolom yang tersedia. Honor Total akan dihitung otomatis oleh sistem.'
            ],
            [
                'Nama Kegiatan',
                'Bulan',
                'Nama PML',
                'Nama PCL',
                'Satuan',
                'Beban / Banyak',
                'Wilayah Tugas',
                'Rate Honor',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [
            [
                'Contoh Kegiatan / Survei',
                'Januari',
                'Contoh Nama PML',
                'Contoh Nama PCL',
                'Segmen',
                5,
                'Contoh Wilayah Tugas',
                70000,
            ],
        ];

        for ($i = 4; $i <= 100; $i++) {
            $rows[] = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Data Survei';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getProtection()->setSheet(true);

        $sheet
            ->getStyle('A4:H100')
            ->getProtection()
            ->setLocked(
                Protection::PROTECTION_UNPROTECTED
            );

        $sheet->mergeCells('A1:H1');

        $sheet
            ->getRowDimension(1)
            ->setRowHeight(30);

        return [

            1 => [
                'font' => [
                    'italic' => true,
                    'size' => 10,
                    'color' => [
                        'rgb' => 'C00000',
                    ],
                ],
                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_LEFT,
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            2 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' =>
                        Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '1F4E79',
                    ],
                ],
                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            3 => [
                'font' => [
                    'italic' => true,
                    'color' => [
                        'rgb' => '595959',
                    ],
                ],
                'fill' => [
                    'fillType' =>
                        Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'D9D9D9',
                    ],
                ],
            ],
        ];
    }
}