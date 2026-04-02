<?php

namespace App\Filament\Resources\Perkembangans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;

class PerkembangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('foto')
                        ->label('Foto')
                        ->circular()
                        ->size(250),
                    TextColumn::make('status__kesimpulan')
                        ->label('Kesimpulan')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'Sesuai Perkembangan' => 'success',
                            'Butuh Stimulasi' => 'warning',
                            'Butuh Rujukan' => 'danger',
                            default => 'gray',
                        })
                         ->sortable(),
                    TextColumn::make('nama_siswa')
                        ->label('Nama Siswa')
                        ->formatStateUsing(fn ($state) => "Nama : {$state}")
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('kelas')
                        ->formatStateUsing(fn ($state) => "Kelas : {$state}")
                        ->label('Kelas')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('nilai_motorik_halus')
                        ->formatStateUsing(function ($state) {
                            if ($state >= 80) {
                                return "Motorik Halus : Sesuai Perkembangan";
                            } elseif ($state >= 60) {
                                return "Motorik Halus : Perlu Stimulasi";
                            } else {
                                return "Motorik Halus : Perlu Rujukan";
                            }
                        })
                        ->label('Nilai Motorik Halus')
                        ->sortable(),
                    TextColumn::make('nilai_motorik_kasar')
                        ->formatStateUsing(function ($state) {
                            if ($state >= 80) {
                                return "Motorik Kasar : Sesuai Perkembangan";
                            } elseif ($state >= 60) {
                                return "Motorik Kasar : Perlu Stimulasi";
                            } else {
                                return "Motorik Kasar : Perlu Rujukan";
                            }
                        })
                        ->label('Nilai Motorik Kasar')
                        ->sortable(),
                    TextColumn::make('nilai_bahasa')
                        ->formatStateUsing(function ($state) {
                            if ($state >= 80) {
                                return "Bahasa : Sesuai Perkembangan";
                            } elseif ($state >= 60) {
                                return "Bahasa : Perlu Stimulasi";
                            } else {
                                return "Bahasa : Perlu Rujukan";
                            }
                        })
                        ->label('Nilai Bahasa')
                        ->sortable(),
                    TextColumn::make('nilai_sosial_kemandirian')
                        ->formatStateUsing(function ($state) {
                            if ($state >= 80) {
                                return "Sosial kemandirian : Sesuai Perkembangan";
                            } elseif ($state >= 60) {
                                return "Sosial kemandirian : Perlu Stimulasi";
                            } else {
                                return "Sosial kemandirian : Perlu Rujukan";
                            }
                        })
                        ->label('Nilai Sosial Kemandirian')
                        ->sortable(),
                    TextColumn::make('created_at')
                        ->label('Waktu Penilaian')
                        ->formatStateUsing(fn ($state) => "Waktu Penilaian : " . $state->format('d M Y'))
                        ->sortable()
                        ->searchable(),
                    
                    //
                ])
            ])

            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])

            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
