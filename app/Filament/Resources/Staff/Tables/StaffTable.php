<?php

namespace App\Filament\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Split: Membelah layout menjadi Kiri dan Kanan
                Split::make([            
                    // SISI KIRI: Foto
                    ImageColumn::make('foto')
                        ->label('Foto')
                        ->circular()
                        ->grow(false) // KUNCI: Biar foto gak melar menuhin layar
                        ->size(100)   // Ukuran dikecilin biar pas di layout grid
                        ->sortable(false), // Foto biasanya gak perlu diurutkan

                    TextColumn::make('nama_guru')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    // SISI KANAN: Tumpukan Teks
                    Stack::make([
                        TextColumn::make('jabatan')
                            ->color('gray')
                            ->size('sm')
                            ->searchable()
                            ->sortable()
                            ->formatStateUsing(fn (string $state): string => __("Jabatan : {$state}")),
                            
                        TextColumn::make('wali_kelas')
                            // KUNCI: Manipulasi output teks biar persis kayak di gambar lu
                            ->formatStateUsing(fn ($state) => $state ? "Wali kelas : {$state}" : "Bukan wali kelas")
                            ->color('gray')
                            ->size('sm')
                            ->sortable(),
                    ])->space(1), // Jarak antar teks dirapatkan
                ]), // Efek belah Kiri-Kanan aktif mulai dari layar tablet/PC
            ])
            // Mengatur jumlah kolom grid
            ->contentGrid([
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
