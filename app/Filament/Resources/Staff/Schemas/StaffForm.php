<?php

namespace App\Filament\Resources\Staff\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;

class StaffForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnspanfull()
                    ->schema([
                        TextInput::make('nama_guru')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('username')
                            ->label('Username')
                            ->required(),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(),
                        Select::make('jabatan')
                            ->options([
                                'Kepala' => 'Kepala',
                                'Staff' => 'Staff',
                                'Guru' => 'Guru',
                                'Guru Pendamping' => 'Guru Pendamping',
                            ])
                            ->required(),
                    ]),
                
                    Grid::make(2)
                        ->columnspanfull()
                        ->schema([
                            Radio::make('wali_kelas')
                                ->options([
                                    'A1' => 'A1',
                                    'A2' => 'A2',
                                    'A3' => 'A3',
                                    'A4' => 'A4',
                                    'B1' => 'B1',
                                    'B2' => 'B2',
                                    '-' => '-',
                                ])
                                ->inline()
                                ->required(),
                            
                            FileUpload::make('foto')
                                ->image()
                                ->imagePreviewHeight('250')
                                ->loadingIndicatorPosition('left')
                                ->panelAspectRatio('2:1')
                                ->panelLayout('integrated')
                                ->removeUploadedFileButtonPosition('right')
                                ->uploadButtonPosition('left')
                                ->uploadProgressIndicatorPosition('left')
                                ->label('Foto')
                                ->directory('foto-guru')
                                ->acceptedFileTypes(['image/*']),
                    ])
                //
            ]);
    }
}
