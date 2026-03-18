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
                        ->size(150),
                    TextColumn::make('nama_siswa')
                        ->label('Nama Siswa')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('kelas')
                        ->label('Kelas')
                        ->sortable()
                        ->searchable(),
                    TextColumn::make('nilai_motorik_halus')
                        ->label('Nilai Motorik Halus')
                        ->sortable(),
                    TextColumn::make('nilai_motorik_kasar')
                        ->label('Nilai Motorik Kasar')
                        ->sortable(),
                    TextColumn::make('nilai_bahasa')
                        ->label('Nilai Bahasa')
                        ->sortable(),
                    TextColumn::make('nilai_sosial_kemandirian')
                        ->label('Nilai Sosial Kemandirian')
                        ->sortable(),
                    //
                ])
            ])

            ->contentGrid([
                'md' => 2,
                'xl' => 5,
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
