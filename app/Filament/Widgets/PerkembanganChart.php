<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Perkembangan;
use Illuminate\Support\Facades\DB;

class PerkembanganChart extends ChartWidget
{
    protected ?string $heading = 'Persentase Hasil Penilaian';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $latestPerkembangan = Perkembangan::whereIn('id', function ($q) {
            $q->select(DB::raw('MAX(id)'))
                ->from('perkembangan')
                ->whereNull('deleted_at')
                ->groupBy('nama_siswa');
        });
        
        $sesuai = (clone $latestPerkembangan)
            ->where(function ($q) {
                $q->where('nilai_motorik_halus', '>=', 80)
                    ->where('nilai_motorik_kasar', '>=', 80)
                    ->where('nilai_bahasa', '>=', 80)
                    ->where('nilai_sosial_kemandirian', '>=', 80);
            })
            ->count();
        
        $butuhStimulasi = (clone $latestPerkembangan)
            ->where(function ($q) {
                $q->whereBetween('nilai_motorik_halus', [60, 79])
                    ->whereBetween('nilai_motorik_kasar', [60, 79])
                    ->whereBetween('nilai_bahasa', [60, 79])
                    ->whereBetween('nilai_sosial_kemandirian', [60, 79]);
            })
            ->count();

        $butuhRujukan = (clone $latestPerkembangan)
            ->where(function ($q) {
                $q->where('nilai_motorik_halus', '<', 60)
                    ->orWhere('nilai_motorik_kasar', '<', 60)
                    ->orWhere('nilai_bahasa', '<', 60)
                    ->orWhere('nilai_sosial_kemandirian', '<', 60);
            })
            ->count();

        $total = Perkembangan::count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penilaian',
                    'data' => [$sesuai, $butuhStimulasi, $butuhRujukan],
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
