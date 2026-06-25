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
                                        continue;
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

                                $html .= "<div>";
                                $html .= "<p class='mb-4 text-base text-gray-800 dark:text-gray-200'> <strong>Kesimpulan Sistem:</strong></p>"; 
                                $html .= "<u>{$teksUtama}</u>";
                                $html .= "</div>";

                                $html .= "<br>";
                                $html .= "<div style='padding-left: 0.25rem;'>";
                                $html .= "<p class='mb-4 text-base text-gray-800 dark:text-gray-200'> <strong>Berikut adalah detail dari perkembangan anak:</strong></p>"; 
                                $html .= "<br>";

                                if (count($butuhRujukan) > 0) {
                                    $domainGagal = implode(', ', $butuhRujukan);
                                    $html .= "<div style='color: #991b1b; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #ef4444; background-color: #fef2f2; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Membutuhkan Rujukan Khusus:</strong>";
                                    $html .= "<strong>{$domainGagal}</strong>";
                                    $html .= "</div>";
                                }

                                if (count($butuhStimulasi) > 0) {
                                    $domainKurang = implode(', ', $butuhStimulasi);
                                    $html .= "<div style='color: #854d0e; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #eab308; background-color: #fefce8; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Membutuhkan Stimulasi Tambahan:</strong>";
                                    $html .= "<strong>{$domainKurang}</strong>";
                                    $html .= "</div>";
                                }

                                if (count($sesuai) > 0) {
                                    $domainSesuai = implode(', ', $sesuai);
                                    $html .= "<div style='color: #166534; margin-bottom: 0.75rem; padding: 0.75rem; border-left: 4px solid #22c55e; background-color: #f0fdf4; border-radius: 0 0.25rem 0.25rem 0;'>";
                                    $html .= "<strong style='display: block; margin-bottom: 0.25rem;'>Domain yang Sesuai dengan Perkembangan:</strong>";
                                    $html .= "<strong>{$domainSesuai}</strong>";
                                    $html .= "</div>";
                                }

                                $html .= "</div>";

                                return new HtmlString($html);
                            }),

                            Actions::make([
                                Action::make('lihat_rekomendasi')
                                    ->label('Lihat Rekomendasi')
                                    ->icon('heroicon-m-clipboard')
                                    ->color('info')
                                    ->button()
                                    ->modalHeading('Rekomendasi Perkembangan')
                                    ->modalDescription('Daftar stimulasi spesifik berdasarkan analisis penilaian domain siswa.')
                                    ->modalWidth('4xl')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Tutup')
                                    ->infolist([
                                        TextEntry::make('rekomendasi_modal_content')
                                            ->hiddenLabel()
                                            ->html()
                                            ->columnSpanFull()
                                            ->state(fn ($record) => new HtmlString(
                                                static::generateRekomendasiBoxes($record)
                                            )),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->alignStart()
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

                        Livewire::make(
                            TrendPerkembanganUsiaChart::class,
                            fn ($record) => [
                                'record' => $record,
                            ]
                        )
                        ->key('chart-perkembangan-usia-'),
                    ])
                ])
                ->columnSpanFull(),

                Livewire::make(
                    TrendPerkembanganChart::class,
                    fn ($record) => [
                        'record' => $record,
                    ]
                )
                ->key('chart-trend-perkembangan-')
                ->columnSpanFull(),
            ]);
    }

    protected static function makeDomainEntry(string $column, string $label): Grid
    {
        return Grid::make(2)
            ->schema(function ($record) use ($column, $label) {
                return [
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

    protected static function generateRekomendasiBoxes($record): string
    {
        $currentUsiaStr = $record->kelompok_usia;
        preg_match('/\d+/', $currentUsiaStr, $matches);
        $currentUsiaInt = isset($matches[0]) ? (int)$matches[0] : 0;
        
        $nextUsiaInt = $currentUsiaInt + 1;
        $nextUsiaStr = $nextUsiaInt <= 6 ? $nextUsiaInt . ' Tahun' : null;

        $html = '<div class="grid grid-cols-1 gap-4">';

        $domains = [
            'motorik_kasar'      => ['Motorik Kasar', 'fisik motorik'],
            'motorik_halus'      => ['Motorik Halus', 'fisik motorik'],
            'bahasa'             => ['Bahasa', 'bahasa'],
            'sosial_kemandirian' => ['Sosial Kemandirian', 'sosial kemandirian'],
        ];

        foreach ($domains as $column => $data) {
            $label = $data[0];
            $jenisRekomDB = $data[1];
            $score = $record->{"nilai_{$column}"};
            
            if (is_null($score)) continue;

            $listHtml = '';
            $rujukanTeksHtml = '';

            if ($score < 60) {
                $statusTeks = "Rujukan Khusus";
                $boxStyle = "border-left: 4px solid #ef4444; background-color: #fef2f2; color: #991b1b;";
                $titleStyle = "color: #991b1b;";
                $badgeStyle = "background-color: #fca5a5; color: #7f1d1d;";

                $rujukanTeksHtml = "<div style='margin-top: 0.5rem; padding: 0.5rem; background-color: #fecaca; border-left: 3px solid #dc2626; border-radius: 0.25rem; font-size: 0.875rem;'>
                                        <strong>Peringatan:</strong> Domain {$label} membutuhkan rujukan ke spesialis.
                                    </div>";
            } else {
                if ($score < 80) {
                    $statusTeks = "Butuh Stimulasi";
                    $boxStyle = "border-left: 4px solid #eab308; background-color: #fefce8; color: #854d0e;";
                    $titleStyle = "color: #854d0e;";
                    $badgeStyle = "background-color: #fde047; color: #713f12;";
                    $targetUsia = $currentUsiaStr; // Tetap di usia saat ini
                } else {
                    $statusTeks = "Sesuai";
                    $boxStyle = "border-left: 4px solid #22c55e; background-color: #f0fdf4; color: #166534;";
                    $titleStyle = "color: #166534;";
                    $badgeStyle = "background-color: #86efac; color: #14532d;";
                    $targetUsia = $nextUsiaStr; // Naik level ke usia selanjutnya
                }

                if ($targetUsia) {
                    $rekomendasiDb = \App\Models\Rekomendasi::where('jenis_rekomendasi', $jenisRekomDB)
                                                    ->where('kelompok_usia', $targetUsia)
                                                    ->pluck('nama_rekomendasi')
                                                    ->toArray();

                    if (count($rekomendasiDb) > 0) {
                        $listHtml = "<strong style='font-size: 0.875rem; display: block; margin-top: 0.75rem;'>Rekomendasi ({$targetUsia}):</strong>";
                        $listHtml .= '<ul style="list-style-type: disc; padding-left: 1.5rem; margin-top: 0.25rem; font-size: 0.875rem;"><li>' . implode('</li><li>', $rekomendasiDb) . '</li></ul>';
                    } else {
                        $listHtml = '<p style="font-size: 0.875rem; font-style: italic; opacity: 0.8; margin-top: 0.5rem;">(Belum ada data rekomendasi tertulis untuk tahap ini)</p>';
                    }
                } else {
                    $listHtml = "<p style='font-size: 0.875rem; font-weight: bold; margin-top: 0.5rem; opacity: 0.9;'>Perkembangan sesuai dan telah mencapai evaluasi batas usia maksimal (6 Tahun).</p>";
                }
            }

            $html .= "<div style='{$boxStyle} padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); margin-bottom: 1rem;'>
                        <div style='display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 0.5rem; margin-bottom: 0.5rem;'>
                            <h3 style='{$titleStyle} font-weight: bold; font-size: 1.125rem; margin: 0; text-transform: uppercase;'>{$label}</h3>
                            <span style='{$badgeStyle} font-size: 0.75rem; font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 9999px;'>{$statusTeks}</span>
                        </div>
                        {$rujukanTeksHtml}
                        {$listHtml}
                    </div>";
        }

        $html .= '</div>';
        
        return $html;
    }
}