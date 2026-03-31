<?php

namespace App\Filament\Resources\Siswas\Tables;

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

class SiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('foto')
                        ->label('foto')
                        ->circular()
                        ->size(200),
                    TextColumn::make('nama_siswa')
                        ->weight(FontWeight::Bold)
                        ->label('Nama Siswa')
                        ->searchable(),
                    TextColumn::make('no_induk')
                        ->label('Nomor Induk'),
                    TextColumn::make('kelas')
                        ->label('Kelas')
                        ->sortable(),
                    TextColumn::make('tanggal_lahir')
                        ->dateTime('d F Y')
                        ->label('Tanggal Lahir'),
                    TextColumn::make('nama_guru')
                        ->label('Guru')
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
