<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\Perkembangan;

class DashoardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $butuhStimulasi = Perkembangan::where('nilai_motorik_halus', '<', 80)
            ->orWhere('nilai_motorik_kasar', '<', 80)
            ->orWhere('nilai_bahasa', '<', 80)
            ->orWhere('nilai_sosial_kemandirian', '<', 80)
            ->count();
        // Menghitung berapa penilaian yang butuh rujukan (nilai di bawah 60)
        $butuhRujukan = Perkembangan::where('nilai_motorik_halus', '<', 60)
            ->orWhere('nilai_motorik_kasar', '<', 60)
            ->orWhere('nilai_bahasa', '<', 60)
            ->orWhere('nilai_sosial_kemandirian', '<', 60)
            ->count();

        return [
            Stat::make('Total Siswa', Siswa::count())
                ->description('Jumlah siswa terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
                
            Stat::make('Total Perekaman', Perkembangan::count())
                ->description('Riwayat penilaian masuk')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Siswa butuh stimulasi', $butuhStimulasi)
                ->description("Terdapat {$butuhStimulasi} siswa membutuhkan stimulasi tambahan")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
                
            Stat::make('Peringatan Rujukan', $butuhRujukan)
                ->description("Terdapat {$butuhRujukan} siswa membutuhkan rujukan")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}

