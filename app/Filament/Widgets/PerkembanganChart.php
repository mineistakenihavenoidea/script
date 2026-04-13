<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkembangan;

class PerkembanganChart extends ChartWidget
{
    protected ?string $heading = 'Persentase Hasil Penilaian';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $sesuai = Perkembangan::where('nilai_motorik_halus', '>=', 80)
            ->where('nilai_motorik_kasar', '>=', 80)
            ->where('nilai_bahasa', '>=', 80)
            ->where('nilai_sosial_kemandirian', '>=', 80)
            ->count();

        $rujukan = Perkembangan::where('nilai_motorik_halus', '<', 60)
            ->orWhere('nilai_motorik_kasar', '<', 60)
            ->orWhere('nilai_bahasa', '<', 60)
            ->orWhere('nilai_sosial_kemandirian', '<', 60)
            ->count();

        $total = Perkembangan::count();
        $stimulasi = $total - ($sesuai + $rujukan);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penilaian',
                    'data' => [$sesuai, $stimulasi, $rujukan],
                    'backgroundColor' => [
                        'green',
                        'yellow',
                        'red',
                    ],
                ],
            ],
            'labels' => ['Sesuai', 'Butuh Stimulasi', 'Butuh Rujukan'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
