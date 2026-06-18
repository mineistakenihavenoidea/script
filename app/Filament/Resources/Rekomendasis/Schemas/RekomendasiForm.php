<?php

namespace App\Filament\Resources\Rekomendasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Radio;
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
                        Radio::make('kelompok_usia')
                            ->label('Kelompok Usia')
                            ->required()
                            ->options([
                                '4 Tahun' => '4 Tahun',
                                '5 Tahun' => '5 Tahun',
                                '6 Tahun' => '6 Tahun',
                            ])
                            ->inline(),
                        Radio::make('jenis_rekomendasi')
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
