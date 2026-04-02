<?php

namespace App\Filament\Resources\Perkembangans\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\Perkembangan;

class PerkembanganStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
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
                
            Stat::make('Peringatan Rujukan', $butuhRujukan)
                ->description('Terdapat nilai < 60')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}

