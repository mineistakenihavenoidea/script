<?php

namespace App\Filament\Resources\Perkembangans\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Siswa;
use App\Models\DomainPerkembangan;

class PerkembanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kelas')
                    ->options([
                                'A1' => 'A1',
                                'A2' => 'A2',
                                'A3' => 'A3',
                                'A4' => 'A4',
                                'B1' => 'B1',
                                'B2' => 'B2',
                            ])
                    ->live()
                    ->afterStateUpdated(fn (Select $component) => $component
                        ->getContainer()
                        ->getComponent('dynamicTypeFields')
                        ->getChildSchema()
                        ->fill()),
                    
                Grid::make(1)
                    ->schema(fn (Get $get): array => match ($get('kelas')) {
                        'A1', 'A2', 'A3', 'A4', 'B1', 'B2' => [
                            Select::make('nama_siswa')
                                ->label('Nama Siswa')
                                ->options(fn (Get $get) => Siswa::query()
                                    ->where('kelas', $get('kelas'))
                                    ->pluck('nama_siswa', 'nama_siswa'))
                                ->required()
                                ->searchable()
                                ->live(),
                        ],
                        default => [],
                    })
                    ->key('dynamicTypeFields'),
                
                Select::make('kelompok_usia')
                    ->label('Kelompok Usia')
                    ->options([
                        '4 Tahun' => '4 Tahun',
                        '5 Tahun' => '5 Tahun',
                        '6 Tahun' => '6 Tahun',
                    ])
                    ->required()
                    ->live(),

                Grid::make(1)
                    ->schema(function (Get $get) {

                        $age = $get('kelompok_usia');

                        if (!$age) return [];

                        $indikators = DomainPerkembangan::where('kelompok_usia', $age)
                            ->get()
                            ->groupBy('domain');

                        $fields = [];

                        foreach ($indikators as $domain => $items) {

                            foreach ($items as $indikator) {

                                $fields[] = Radio::make("indikator_{$indikator->id}")
                                    ->label($indikator->indikator)
                                    ->options([
                                        'yes' => 'Ya',
                                        'no' => 'Tidak',
                                    ])
                                    ->inline();
                            }
                        }

                        return $fields;

                    })
                    ->key('indicatorFields')
                    //
            ]);
    }
}
