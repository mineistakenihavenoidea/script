<?php

namespace App\Filament\Resources\Rekomendasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Radio;

class RekomendasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnspanfull()
                    ->schema([
                        TextInput::make('nama_rekomendasi')
                            ->label('Nama Rekomendasi')
                            ->required(),
                        TextInput::make('jenis_rekomendasi')
                            ->label('Jenis Rekomendasi')
                            ->required(),
                    ])
                //
            ]);
    }
}
