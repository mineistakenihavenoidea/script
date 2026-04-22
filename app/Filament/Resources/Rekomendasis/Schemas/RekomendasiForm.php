<?php

namespace App\Filament\Resources\Rekomendasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Radio;
use Filament\Forms\Components\Select;

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
                        Select::make('jenis_rekomendasi')
                            ->label('Jenis Rekomendasi')
                            ->required()
                            ->options([                                
                                'Bahasa' => 'Bahasa',
                                'Motorik Halus' => 'Motorik Halus',
                                'Motorik Kasar' => 'Motorik Kasar',
                                'Sosial Kemandirian' => 'Sosial Kemandirian',
                            ]),
                    ])
                //
            ]);
    }
}
