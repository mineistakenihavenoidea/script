<?php


namespace App\Filament\Resources\Perkembangans\Schemas;


use Filament\Schemas\Schema;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\HtmlString;
use App\Models\Rekomendasi;
use App\Models\Siswa;
use App\Models\Staff;
use App\Models\DomainPerkembangan;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;


class PerkembanganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                ->schema([
                    // LEFT (this one can grow vertically)
                    Grid::make(1)
                    ->schema([
                        Section::make ('Informasi Siswa')
                        ->schema([
                            Grid::make(3)
                            ->schema([
                                ImageEntry::make('foto')
                                ->hiddenLabel()
                                ->formatStateUsing(fn ($state) => new HtmlString('<img src="/storage/' . $state . '" style="max-height: 80px; max-width: 80px; object-fit: cover; border-radius: 8px;">'))
                                ->visible(fn ($state) => filled($state))
                                ->size(175),
                                // DATA (KANAN)
                                Grid::make(2)
                                ->schema([
                                    TextEntry::make('nama_siswa')->weight('bold'),
                                    TextEntry::make('kelas'),
                                    TextEntry::make('pengisi')
                                    ->label('Pengisi Perkembangan'),
                                ])
                                ->columnSpan(2),
                            ]),
                        ]),


                        Section::make('Kesimpulan dan Rekomendasi')
                        ->schema([
                            TextEntry::make('kesimpulan_sistem')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->html()
                            ->state(function ($record) {
                                $domains = [
                                    'motorik halus' => $record->nilai_motorik_halus,
                                    'motorik kasar' => $record->nilai_motorik_kasar,
                                    'bahasa' => $record->nilai_bahasa,
                                    'sosial kemandirian' => $record->nilai_sosial_kemandirian,
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


                                $totalTargetIndikator = \App\Models\DomainPerkembangan::where('kelompok_usia', $record->kelompok_usia)->count();
                                
                                $jumlahTerjawab = is_array($record->detail_indikator) ? count($record->detail_indikator) : 0;


                                if ($jumlahTerjawab < $totalTargetIndikator) {
                                    return 'Data belum terisi sepenuhnya';
                                } elseif (count($butuhRujukan) > 0) {
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
                    ])
                    ->columnSpan(1),


                    Grid::make(1)
                    ->schema([
                        Section::make('Hasil Penilaian')
                            ->description('Hasil penilaian per Domain')
                            ->schema([
                                Grid::make(2)
                                ->schema([
                                    self::makeDomainEntry('motorik_halus', 'Motorik Halus'),
                                    self::makeDomainEntry('motorik_kasar', 'Motorik Kasar'),
                                    self::makeDomainEntry('bahasa', 'Bahasa'),
                                    self::makeDomainEntry('sosial_kemandirian', 'Sosial Kemandirian'),
                                ]),                           
                            ])
                            ->columnSpan(1),
                        Actions::make([
                            Action::make('printPdf')
                                ->label('PDF')
                                ->icon('heroicon-m-printer')
                                ->color('success')
                                ->button()
                                ->requiresConfirmation()
                                ->action(function ($record) {
                                    return redirect()->away(
                                        route('perkembangan.print', ['id' => $record->id])
                                    );
                                })
                        ])->fullWidth(),
                        // RIGHT SIDE (independent layout)
                    ])
                ])
                ->columnSpanFull(),
            ]);
    }


    protected static function makeDomainEntry(string $column, string $label): Grid
    {
        return Grid::make(2)
            ->schema(function ($record) use ($column, $label) {
                $totalTargetIndikator = \App\Models\DomainPerkembangan::where('kelompok_usia', $record->kelompok_usia)->count();
                $jumlahTerjawab = is_array($record->detail_indikator) ? count($record->detail_indikator) : 0;


                if ($jumlahTerjawab < $totalTargetIndikator) {
                        return [
                        Placeholder::make("nilai_{$column}")
                            ->label($label)
                            ->content('Data belum terisi sepenuhnya')
                    ];
                }


                return [
                    // Score text
                    TextEntry::make("nilai_{$column}")
                        ->label($label)
                        ->formatStateUsing(fn ($state) => round($state) . ' / 100'),

                    TextEntry::make("nilai_{$column}")
                        ->hiddenLabel() // no duplicate label
                        ->formatStateUsing(fn ($state, $record) => ucwords($record->classifyScore($state)))
                        ->color(fn ($state) =>
                            $state >= 80 ? 'success' :
                            ($state >= 60 ? 'warning' : 'danger'))
                        ->badge(),
                    // Badge classification
                    // TextEntry::make("nilai_{$column}")
                    //     ->hiddenLabel() // no duplicate label
                    //     ->html()
                    //     ->formatStateUsing(function ($state, $record) {
                            
                    //         $teksStatus = ucwords($record->classifyScore($state));

                    //         if ($state >= 80) {
                    //             $bg = '#10B58A'; // success
                    //         } elseif ($state >= 60) {
                    //             $bg = '#F59E0B'; // warning
                    //         } else {
                    //             $bg = '#EF4444'; // danger
                    //         }

                    //         return new \Illuminate\Support\HtmlString("
                    //             <span style='background-color: {$bg}; color: #ffffff; padding: 4px 10px; border-radius: 9999px; 
                    //             font-size: 0.85em; font-weight: bold; border: 1px solid rgba(255,255,255,0.2);'>
                    //                 {$teksStatus}
                    //             </span>
                    //         ");
                    //     }),
                ];
            })
            ->columnSpanFull();
    }
}