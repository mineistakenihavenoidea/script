<?php

namespace App\Filament\Resources\Perkembangans\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Siswa;
use App\Models\DomainPerkembangan;
use Carbon\Carbon;

class PerkembanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('nama_siswa')
                            ->label('Nama Siswa')
                            ->options(Siswa::query()->pluck('nama_siswa', 'nama_siswa'))
                            ->live()
                            ->required()
                            ->searchable()
                            ->afterStateUpdated(function ($state, Set $set){
                                if ($state) {
                                    $siswa = Siswa::where('nama_siswa', $state)->first();
                                    if ($siswa) {
                                        $set('kelas', $siswa->kelas ?? null);
                                        $set('foto', $siswa->foto ?? null);
                                        
                                        if ($siswa->tanggal_lahir) {
                                            $umur = Carbon::parse($siswa->tanggal_lahir)->age;
                                            if ($umur <= 4) $set('kelompok_usia', '4 Tahun');
                                            elseif ($umur > 4 && $umur < 6) $set('kelompok_usia', '5 Tahun');
                                            elseif ($umur >= 6) $set('kelompok_usia', '6 Tahun');
                                        }
                                    }
                                } else {
                                    $set('kelas', null);
                                    $set('foto', null);
                                    $set('kelompok_usia', null);
                                }
                            }),

                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->readOnly()
                            ->required(),
                    ]),
                
                    
                Grid::make(2)
                    ->schema([
                        TextInput::make('kelompok_usia')
                            ->label('Kelompok Usia')
                            ->readOnly()
                            ->dehydrated(false)
                            ->live()
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                $namaSiswa = $get('nama_siswa');
                                if ($namaSiswa) {
                                    $siswa = Siswa::where('nama_siswa', $namaSiswa)->first();
                                    if ($siswa && $siswa->tanggal_lahir) {
                                        $umur = Carbon::parse($siswa->tanggal_lahir)->age;
                                        if ($umur <= 4) $set('kelompok_usia', '4 Tahun');
                                        elseif ($umur > 4 && $umur < 6) $set('kelompok_usia', '5 Tahun');
                                        elseif ($umur >= 6) $set('kelompok_usia', '6 Tahun');
                                    }
                                }
                            })
                            ->required(),
                        Hidden::make('foto'),

                        Placeholder::make('foto_preview')
                            ->label('Foto Siswa')
                            ->content(function (Get $get) {
                                $foto = $get('foto');
                                if ($foto) {
                                    return new HtmlString('<img src="/storage/' . $foto . '" style="max-height: 150px; border-radius: 8px; border: 1px solid #444; object-fit:cover;">');
                                }
                                return 'Tidak ada foto';
                            }),
                    ]),
                
                Grid::make(1)
                    ->schema(function (Get $get) {
                        $age = $get('kelompok_usia');

                        if (!$age) return [];

                        $indikators = DomainPerkembangan::where('kelompok_usia', $age)
                            ->get()
                            ->groupBy('domain');

                        $fields = [];

                        foreach ($indikators as $domain => $items) {
                            $domainTitle = ucwords(str_replace('_', ' ', $domain));
                            $fields[] = Placeholder::make("judul_{$domain}")
                                ->label('')
                                ->content(new HtmlString("<div style='font-weight: 600; font-size: 1.1em; margin-top: 1rem;'>Domain: {$domainTitle}</div>"));

                            foreach ($items as $indikator) {
                                $fields[] = Radio::make("indikator_{$indikator->id}")
                                    ->label($indikator->indikator)
                                    ->options([
                                        'yes' => 'Ya',
                                        'no' => 'Tidak',
                                    ])
                                    ->formatStateUsing(function (?Model $record) use ($indikator) {
                                        if ($record && is_array($record->detail_indikator)) {
                                            return $record->detail_indikator["indikator_{$indikator->id}"] ?? null;
                                        }
                                        return null;
                                    })
                                    ->dehydrated(true)
                                    ->inline()
                                    ->required();
                            }
                        }

                        return $fields;
                    })
                    ->key('indicatorFields')
                    //
            ]);
    }
}
