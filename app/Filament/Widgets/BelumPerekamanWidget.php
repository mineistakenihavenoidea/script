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
        $now = now();
        $currentStartYear = $now->month >= 7 ? $now->year : $now->year - 1;
        // build array ONCE
        $angkatanAktif = [];
        for ($i = 0; $i < 3; $i++) {
            $year = $currentStartYear - $i;
            $angkatanAktif[] = "{$year}/" . ($year + 1);
        }

        $startDate = Carbon::create($currentStartYear - 2, 7, 1)->startOfDay();
        $endDate   = Carbon::create($currentStartYear + 1, 6, 30)->endOfDay();

        return $table
            ->heading('Siswa Belum Perekaman Bulan Ini')
            ->description('Daftar siswa yang belum memiliki perkembangan terbaru bulan ini')
            ->query(
                Siswa::query()
                    ->whereIn('ta_masuk', $angkatanAktif) // ✅ FIXED
                    ->whereDoesntHave('perkembangan', function (Builder $query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
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
