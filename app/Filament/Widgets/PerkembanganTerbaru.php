<?php

namespace App\Filament\Widgets;

use App\Models\Perkembangan;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PerkembanganTerbaru extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Perkembangan::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('nama_siswa')
                    ->label('Nama Siswa')
                    ->weight('bold'),
                TextColumn::make('kelas')
                    ->label('Kelas'),
                TextColumn::make('created_at')
                    ->label('Waktu Penilaian')
                    ->formatStateUsing(fn ($state) => "Waktu Penilaian : " . $state->format('d M Y')),
                TextColumn::make('status_kesimpulan')
                    ->label('Kesimpulan')
                    ->badge()
                    ->state(function (Perkembangan $record): string {
                        $minScore = min([
                            $record->nilai_motorik_halus ?? 0,
                            $record->nilai_motorik_kasar ?? 0,
                            $record->nilai_bahasa ?? 0,
                            $record->nilai_sosial_kemandirian ?? 0,
                        ]);

                        if ($minScore < 60) return 'Butuh Rujukan';
                        if ($minScore < 80) return 'Butuh Stimulasi';
                        return 'Sesuai Perkembangan';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Sesuai Perkembangan' => 'success',
                        'Butuh Stimulasi' => 'warning',
                        'Butuh Rujukan' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)

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
