<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\Perkembangan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashoardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Menentukan Tahun Ajaran Saat Ini (Juli - Juni)
        $startYear = now()->month >= 7 ? now()->year : now()->year - 1;
        $startDate = Carbon::create($startYear, 7, 1)->startOfDay();
        $endDate = Carbon::create($startYear + 1, 6, 30)->endOfDay();
        
        // Label teks untuk ditampilkan di widget
        $labelTahunAjaran = "T.A. {$startYear}/" . ($startYear + 1);

        $latestPerkembangan = Perkembangan::whereIn('id', function ($q) {
            $q->select(DB::raw('MAX(id)'))
                ->from('perkembangan')
                ->whereNull('deleted_at')
                ->groupBy('nama_siswa');
        });

        $butuhStimulasi = (clone $latestPerkembangan)
            ->where(function ($q) {
                $q->whereBetween('nilai_motorik_halus', [60, 79])
                    ->whereBetween('nilai_motorik_kasar', [60, 79])
                    ->whereBetween('nilai_bahasa', [60, 79])
                    ->whereBetween('nilai_sosial_kemandirian', [60, 79]);
            })
            ->count();

        // Menghitung berapa penilaian yang butuh rujukan (nilai di bawah 60)
        $butuhRujukan = (clone $latestPerkembangan)
            ->where(function ($q) {
                $q->where('nilai_motorik_halus', '<', 60)
                    ->orWhere('nilai_motorik_kasar', '<', 60)
                    ->orWhere('nilai_bahasa', '<', 60)
                    ->orWhere('nilai_sosial_kemandirian', '<', 60);
            })
            ->count();

        return [
            Stat::make('Total Siswa', Siswa::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description($labelTahunAjaran)
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Data Perkembangan', Perkembangan::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description($labelTahunAjaran)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
            // Tambahkan model/data lain di sini jika ada, menggunakan whereBetween yang sama
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

