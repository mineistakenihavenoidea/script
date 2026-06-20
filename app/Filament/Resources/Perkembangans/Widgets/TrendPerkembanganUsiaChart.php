<?php

namespace App\Filament\Resources\Perkembangans\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use App\Models\Perkembangan;
use Carbon\Carbon;

class TrendPerkembanganUsiaChart extends ChartWidget
{
    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Perkembangan Anak Dalam Kelompok Usia';

    public ?Model $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $riwayat = Perkembangan::where('nama_siswa', $this->record->nama_siswa)
            ->orderBy('created_at', 'asc')
            ->get()
            ->unique('kelompok_usia')
            ->sortBy('kelompok_usia');

        $categories = [];
        $motorikKasar = [];
        $motorikHalus = [];
        $bahasa = [];
        $sosialKemandirian = [];

        foreach ($riwayat as $row) {
            if (!$row->kelompok_usia) continue;

            $categories[] = $row->kelompok_usia;
            $motorikKasar[] = round($row->nilai_motorik_kasar ?? 0);
            $motorikHalus[] = round($row->nilai_motorik_halus ?? 0);
            $bahasa[] = round($row->nilai_bahasa ?? 0);
            $sosialKemandirian[] = round($row->nilai_sosial_kemandirian ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Motorik Kasar',
                    'data' => $motorikKasar,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Motorik Halus',
                    'data' => $motorikHalus,
                    'backgroundColor' => '#ef4444',
                ],
                [
                    'label' => 'Bahasa',
                    'data' => $bahasa,
                    'backgroundColor' => '#facc15',
                ],
                [
                    'label' => 'Sosial Kemandirian',
                    'data' => $sosialKemandirian,
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $categories,
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
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
