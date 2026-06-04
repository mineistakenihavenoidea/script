<?php

namespace App\Filament\Widgets;

use App\Models\Perkembangan;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\Siswa;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class BelumPerekamanWidget extends TableWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        // 1. Menentukan Tahun Ajaran Saat Ini (Juli - Juni)
        $startYear = now()->month >= 7 ? now()->year : now()->year - 1;
        $startDate = Carbon::create($startYear, 7, 1)->startOfDay();
        $endDate = Carbon::create($startYear + 1, 6, 30)->endOfDay();

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

        $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
        $currentTaYearOne = ($currentStartYear - 1) . "/{$currentStartYear}";
        $currentTaYearTwo = "{$currentStartYear}/" . ($currentStartYear + 1);

        $activeTa = [$currentTaYearOne, $currentTaYearTwo];

        $now = now();

        return $table
            ->heading('Siswa Belum Perekaman Bulan Ini')
            ->description('Daftar siswa yang belum memiliki perkembangan terbaru bulan ini')
            ->query(
                Siswa::query()
                    ->whereIn('ta_masuk', $activeTa) // ✅ FIXED
                    ->whereDoesntHave('perkembangan', function (Builder $query) {
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                    })
            )
            ->columns([
                TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->weight('bold'),
                TextColumn::make('kelas')
                    ->label('Kelas'),
                //
            ])
            ->emptyStateHeading('Semua siswa sudah direkam bulan ini!')
            ->emptyStateIcon('heroicon-m-check-badge')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
