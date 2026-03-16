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
                    TextColumn::make('nama_siswa')
                        ->label('Nama Siswa')
                        ->searchable(),
                    TextColumn::make('nama_guru')
                        ->label('Nama Guru')
                        ->searchable()
                        ->sortable(),
                        TextColumn::make('kelas')
                        ->label('Kelas')
                        ->sortable(),
                    TextColumn::make('nilai_motorik_halus')
                        ->label('Nilai Motorik Halus'),
                    TextColumn::make('nilai_motorik_kasar')
                        ->label('Nilai Motorik Kasar'),
                    TextColumn::make('nilai_bahasa')
                        ->label('Nilai Bahasa'),
                    TextColumn::make('nilai_sosial_kemandirian')
                        ->label('Nilai Sosial Kemandirian'),
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
