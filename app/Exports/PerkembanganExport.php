<?php

namespace App\Exports;

use App\Models\Perkembangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PerkembanganExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    // Ambil query data perkembangan, jangan lupa load relasi siswa 
    // untuk ambil nomor induknya.
    public function query()
    {
        return Perkembangan::query()->with('siswa');
    }
    // * Map data ke kolom spreadsheet (Sesuai request lo: Tanpa JSON)
    public function map($perkembangan): array
    {
        return [
            $perkembangan->nama_siswa,
            $perkembangan->siswa->no_induk ?? '-',
            $perkembangan->kelas,
            $perkembangan->nilai_motorik_halus,
            $perkembangan->nilai_motorik_kasar,
            $perkembangan->nilai_bahasa,
            $perkembangan->nilai_sosial_kemandirian,
            $perkembangan->status_kesimpulan,
            // Add more fields as needed
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Nomor Induk',
            'Kelas',
            'Nilai Motorik Halus',
            'Nilai Motorik Kasar',
            'Nilai Bahasa',
            'Nilai Sosial Kemandirian',
            'Status Kesimpulan',
            // Add more headings as needed
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $tabelRange = 'A1:' . $highestColumn . $highestRow;
        $headerRange = 'A1:' . $highestColumn . '1';

        $sheet->getStyle($tabelRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCCCCCC'],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);
    }
}
