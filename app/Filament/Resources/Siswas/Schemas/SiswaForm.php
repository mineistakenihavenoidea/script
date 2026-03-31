<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use App\Models\Staff;
use App\Filament\Resources\Siswas\Schemas\FeatureFlag;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnspanfull()
                    ->schema([
                            TextInput::make('nama_siswa')
                                ->label('Nama')
                                ->required(),
                            TextInput::make('no_induk')
                                ->label('Nomor Induk')
                                ->required(),
                            DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->displayFormat('d F Y')
                                ->required(),
                            Radio::make('kelas')
                                ->label('Kelas')
                                ->options([
                                    'A1' => 'A1',
                                    'A2' => 'A2',
                                    'A3' => 'A3',
                                    'A4' => 'A4',
                                    'B1' => 'B1',
                                    'B2' => 'B2',
                                ])
                                ->inline()
                                ->required(),
                            Select::make('nama_guru')
                                ->label('Guru')
                                ->relationship('guru', 'nama_guru')
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
                                ->directory('foto-siswa')
                                ->acceptedFileTypes(['image/*']),
                    ])
                //
            ]);
    }
}
