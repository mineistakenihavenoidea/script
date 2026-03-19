<?php

namespace App\Filament\Resources\Perkembangans\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\HtmlString;
use App\Models\Rekomendasi;

class PerkembanganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make ('Informasi Siswa')
                    ->schema([
                        Grid::make(3)
                        ->schema([
                            TextEntry::make('nama_siswa')
                                ->label('Nama Siswa')
                                ->weight('bold'),
                            TextEntry::make('kelas')
                                ->label('Kelas'),
                            TextEntry::make('foto')
                                ->label('Foto')
                                ->formatStateUsing(fn ($state) => new HtmlString('<img src="/storage/' . $state . '" style="max-height: 80px; border-radius: 8px;">'))
                                ->visible(fn ($state) => filled($state)),
                        ]),
                    ]),

                Section::make('Hasil Penilaian')
                    ->description('Berikut adalah hasil penilaian per Domain.')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        self::makeDomainEntry('motorik_halus', 'Motorik Halus'),
                                        self::makeDomainEntry('motorik_kasar', 'Motorik Kasar'),
                                        self::makeDomainEntry('bahasa', 'Bahasa'),
                                        self::makeDomainEntry('sosial_kemandirian', 'Sosial Kemandirian'),
                                    ]),
                            ]),
                        
                Section::make('Kesimpulan dan Rekomendasi')
                ->schema([
                    TextEntry::make('kesimpulan_sistem')
                    ->label('')
                    ->html()
                    ->state(function ($record) {
                        $domains = [
                            'motorik_halus' => $record->nilai_motorik_halus,
                            'motorik_kasar' => $record->nilai_motorik_kasar,
                            'bahasa' => $record->nilai_bahasa,
                            'sosial_kemandirian' => $record->nilai_sosial_kemandirian,
                        ];

                        $butuhStimulasi = [];
                        $butuhRujukan = [];

                        foreach ($domains as $name => $score) {
                            if ($score < 60) {
                                $butuhRujukan[] = ucwords($name);
                            } elseif ($score < 80) {
                                $butuhStimulasi[] = $name;
                            }
                        }

                        if (count($butuhRujukan) > 0) {
                            $domainGagal = implode(',', $butuhRujukan);
                            return "<div style='padding: 1rem; border-radius: 0.5rem; background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171;'>
                                <strong>SISWA MEMBUTUHKAN RUJUKAN KHUSUS:</strong><br>
                                Terdapat perkembangan yang tidak sesuai perkembangan dan memerlukan rujukan yakni pada domain: <strong>{$domainGagal}</strong>. Disarankan untuk segera melakukan konsultasi dengan profesional.
                                </div>";
                        } elseif (count($butuhStimulasi) > 0) {
                            $html = "<div style='padding: 1rem; border-radius: 0.5rem; background-color: #fef9c3; color: #9a3412; border: 1px solid #fde047;'>
                                <strong>SISWA MEMBUTUHKAN STIMULASI:</strong><br>
                                Terdapat perkembangan yang kurang optimal. Disarankan untuk memberikan stimulasi berikut sebagai tambahan di area tersebut <br><br>
                                <ul style='margin-left: 1.5rem; list-style-type: disc;'>";
                            
                            foreach ($butuhStimulasi as $jenis) {
                                $namaDomain = ucwords($jenis);
                                $html .= "<li><strong>{$namaDomain}:</strong>";

                                $rekomendasiDb = Rekomendasi::where('jenis_rekomendasi', $jenis)->pluck('nama_rekomendasi')->toArray();

                                if(count($rekomendasiDb) > 0) {
                                    $html .= implode("; ", $rekomendasiDb) . "</li>";
                                } else {
                                    $html .= "<em>(Belum ada data rekomendasi di database)</em></li>";
                                }
                            }

                            $html .= "</ul></div>";
                            return $html;

                        } else {
                            return "<div style='padding: 1rem; border-radius: 0.5rem; background-color: #dcfce3; color: #166534; border: 1px solid #86efac;'>
                                <strong>PERKEMBANGAN SISWA SESUAI:</strong><br>
                                Siswa menunjukkan perkembangan yang sesuai dengan usianya pada semua domain. Tetap berikan stimulasi yang baik untuk mendukung perkembangan optimal.
                                </div>";
                        }
                    }),
                ]),
            ]),
        ]);
    }

    protected static function makeDomainEntry(string $column, string $label): TextEntry
    {
        return TextEntry::make("nilai_{$column}")
            ->label($label)
            ->formatStateUsing(fn ($state, $record) => round($state) . '%% - ' . ucwords($record->classifyScore($state)))
            ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger'))
            ->badge();
    }
}