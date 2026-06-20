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
use Filament\Schemas\Components\Livewire;
use App\Filament\Resources\Perkembangans\Widgets\TrendPerkembanganChart;
use App\Filament\Resources\Perkembangans\Widgets\TrendPerkembanganUsiaChart;


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
                                    ->label('Pengisi'),
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
                                $sesuai = [];

                                $totalTargetIndikator = \App\Models\DomainPerkembangan::where('kelompok_usia', $record->kelompok_usia)
                                    ->where(function ($query) use ($record) { 
                                        $query->whereNull('created_at')
                                        ->orWhere('created_at', '<=', $record->created_at);
                                    })
                                    ->count();
                                $jumlahTerjawab = is_array($record->detail_indikator) ? count($record->detail_indikator) : 0;

                                foreach ($domains as $name => $score) {
                                    if ($jumlahTerjawab < $totalTargetIndikator) {
                                    return 'Data belum terisi sepenuhnya';
                                    }
                                    if (is_null($score)) {
                                        continue; // Lewati domain yang belum dinilai
                                    }
                                    if ($score < 60) {
                                        $butuhRujukan[] = ucwords($name);
                                    } elseif ($score < 80) {
                                        $butuhStimulasi[] = ucwords($name);
                                    } else {
                                        $sesuai[] = ucwords($name);
                                    }
                                }

                                if (count($butuhRujukan) > 0) {
                                    $teksUtama = "SISWA MEMBUTUHKAN RUJUKAN KHUSUS";
                                    $bgUtama = '#fee2e2';
                                    $borderUtama = '#f87171';
                                    $warnaTeksUtama = '#991b1b';
                                } elseif (count($butuhStimulasi) > 0) {
                                    $teksUtama = "SISWA MEMBUTUHKAN STIMULASI";
                                    $bgUtama = '#fef9c3';
                                    $borderUtama = '#facc15';
                                    $warnaTeksUtama = '#854d0e';
                                } else {
                                    $teksUtama = "PERKEMBANGAN SISWA SESUAI";
                                    $bgUtama = '#dcfce3';
                                    $borderUtama = '#4ade80';
                                    $warnaTeksUtama = '#166534';
                                }

                                $html = "";

                                // KOTAK KESIMPULAN UTAMA (BOX BESAR)
                                $html .= "<div>";
                                $html .= "<p class='mb-4 text-base text-gray-800 dark:text-gray-200'> <strong>Kesimpulan Sistem:</strong></p>"; 
                                $html .= "<u>{$teksUtama}</u>";
                                $html .= "</div>";

                                // 2. BAGIAN DETAIL (DINAMIS & BERWARNA)
                                $html .= "<br>";
                                $html .= "<div style='padding-left: 0.25rem;'>";
                                $html .= "<p class='mb-4 text-base text-gray-800 dark:text-gray-200'> <strong>Berikut adalah detail dari perkembangan anak:</strong></p>"; 
                                $html .= "<br>";


                                // Jika butuh rujukan, tampilkan detail merah
                                if (count($butuhRujukan) > 0) {
                                    $domainGagal = implode(', ', $butuhRujukan);
                                    $html .= "<div style='color: #991b1b; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Membutuhkan Rujukan Khusus:</strong>";
                                    $html .= "Domain <strong>{$domainGagal}</strong>. Disarankan untuk segera melakukan konsultasi.";
                                    $html .= "</div>";
                                }

                                // Jika butuh stimulasi, tampilkan detail kuning beserta list rekomendasinya
                                if (count($butuhStimulasi) > 0) {
                                    $html .= "<div style='color: #854d0e; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #eab308; background-color: #fefce8; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Membutuhkan Stimulasi Tambahan:</strong>";
                                    $html .= "<ul style='margin-top: 0.25rem; margin-bottom: 0; padding-left: 1.25rem;'>";
                                    
                                    foreach ($butuhStimulasi as $jenis) {
                                        $rekomendasiDb = \App\Models\Rekomendasi::where('jenis_rekomendasi', $jenis)->pluck('nama_rekomendasi')->toArray();
                                        $teksRekomendasi = count($rekomendasiDb) > 0 
                                            ? implode("; ", $rekomendasiDb) 
                                            : "<em>(Belum ada data rekomendasi di database)</em>";

                                        $html .= "<li style='margin-bottom: 0.25rem;'><strong>" . ucwords($jenis) . ":</strong> " . $teksRekomendasi . "</li>";
                                    }
                                    $html .= "</ul></div>";
                                }

                                // Jika ada yang sesuai, tampilkan detail hijaunya
                                if (count($sesuai) > 0) {
                                    $domainSesuai = implode(', ', $sesuai);
                                    $html .= "<div style='color: #166534; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #22c55e; background-color: #f0fdf4; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Sesuai dengan Perkembangan:</strong>";
                                    $html .= "Domain <strong>{$domainSesuai}</strong> telah berkembang dengan baik sesuai usianya.";
                                    $html .= "</div>";
                                }

                                if (count($butuhStimulasi) > 0) {
                                    $html .= "<div style='color: #854d0e; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #08aaea; background-color: #e8f7fe; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Membutuhkan Stimulasi Tambahan:</strong>";
                                    $html .= "<ul style='margin-top: 0.25rem; margin-bottom: 0; padding-left: 1.25rem;'>";
                                    
                                    foreach ($butuhStimulasi as $jenis) {
                                        $rekomendasiDb = \App\Models\Rekomendasi::where('jenis_rekomendasi', $jenis)->pluck('nama_rekomendasi')->toArray();
                                        $teksRekomendasi = count($rekomendasiDb) > 0 
                                            ? implode("; ", $rekomendasiDb) 
                                            : "<em>(Belum ada data rekomendasi di database)</em>";

                                        $html .= "<li style='margin-bottom: 0.25rem;'><strong>" . ucwords($jenis) . ":</strong> " . $teksRekomendasi . "</li>";
                                    }
                                    $html .= "</ul></div>";
                                }

                                $html .= "</div>"; // End Detail container

                                return new HtmlString($html);
                            }),
                        ])
                        ->columnSpan(1),
                    ]),

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
                                ->action(function ($record, $livewire) {
                                    $url = route('perkembangan.print', ['id' => $record->id]);

                                    $livewire->js("window.open('{$url}', '_blank');");
                                })
                        ])->fullWidth(),
                        // RIGHT SIDE (independent layout)
                        Livewire::make(
                            TrendPerkembanganUsiaChart::class,
                            fn ($record) => [
                                'record' => $record,
                            ]
                        ),
                    ])
                ])
                ->columnSpanFull(),
                Livewire::make(
                    TrendPerkembanganChart::class,
                    fn ($record) => [
                        'record' => $record,
                    ]
                )
                ->columnSpanFull(),
            ]);
    }

    protected static function makeDomainEntry(string $column, string $label): Grid
    {
        return Grid::make(2)
            ->schema(function ($record) use ($column, $label) {

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