<?php

namespace App\Filament\Resources\Perkembangans\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use App\Models\Perkembangan;
use Carbon\Carbon;

class TrendPerkembanganChart extends ChartWidget
{
    protected ?string $heading = 'Perkembangan Siswa';

    public ?Model $record = null;

    protected function getData(): array
    {
        $historyData = Perkembangan::where('nama_siswa', $this->record->nama_siswa)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Motorik Kasar',
                    'data' => $historyData->map(fn ($item) => (float) $item->nilai_motorik_kasar)->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Motorik Halus',
                    'data' => $historyData->map(fn ($item) => (float) $item->nilai_motorik_halus)->toArray(),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Bahasa',
                    'data' => $historyData->map(fn ($item) => (float) $item->nilai_bahasa)->toArray(),
                    'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Sosial Kemandirian',
                    'data' => $historyData->map(fn ($item) => (float) $item->nilai_sosial_kemandirian)->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ],
            ],

            'labels' => $historyData->map(fn ($item) => Carbon::parse($item->created_at)->translatedFormat('M Y'))->toArray(),
            //
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '300px';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 10,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
