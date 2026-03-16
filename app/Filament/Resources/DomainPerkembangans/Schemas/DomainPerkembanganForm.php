<?php

namespace App\Filament\Resources\DomainPerkembangans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;

class DomainPerkembanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('domain')
                ->label('Domain')
                ->required()
                ->options([
                    'Motorik Kasar' => 'Motorik Kasar',
                    'Motorik Halus' => 'Motorik Halus',
                    'Bahasa' => 'Bahasa',
                    'Sosial-Kemandirian' => 'Sosial-Kemandirian',
                    // Add more options as needed
                ])
                ->inline(),
                Radio::make('kelompok_usia')
                ->label('Kelompok Usia')
                ->required()
                ->options([
                    '4 Tahun' => '4 Tahun',
                    '5 Tahun' => '5 Tahun',
                    '6 Tahun' => '6 Tahun',
                    // Add more options as needed
                ])
                ->inline(),
                TextInput::make('indikator')
                ->label('Indikator')
                ->required(),
            ]);
    }
}
