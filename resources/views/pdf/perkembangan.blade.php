@php
    $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
    $currentTa = "{$currentStartYear}/" . ($currentStartYear + 1);
@endphp

<!DOCTYPE html>
    <html lang="id">
    <head>
        <title>Laporan Perkembangan Siswa</title>
        <style>
            body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #000; line-height: 1.4; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
            .header p { margin: 3px 0 0; font-size: 12px; color: #333; }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .info-table td { padding: 6px 10px; vertical-align: top; border: none; }
            .info-label { width: 18%; font-weight: bold; color: #000; }
            .info-value { width: 32%; }
            
            .domain-section { margin-bottom: 15px; page-break-inside: avoid; }
            .domain-title { background: #e5e7eb; border: 1px solid #000; padding: 8px; margin: 0; font-weight: bold; }
            
            .score-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .score-table th, .score-table td { border: 1px solid #000; padding: 6px; text-align: left; }
            .score-table th { background: #f3f4f6; font-weight: bold; text-align: center; }
            
            /* BOX HITAM PUTIH FORMAL */
            .box-conclusion { border: 2px solid #000; padding: 12px; margin-bottom: 15px; background-color: #f9fafb; }
            .box-conclusion-title { font-weight: bold; font-size: 13px; text-transform: uppercase; border-bottom: 1px dashed #000; padding-bottom: 4px; margin-bottom: 6px; display: block; }
            
            .alert-detail { border: 1px solid #000; padding: 10px; margin-bottom: 10px; background-color: #fff; }
            .alert-title { font-weight: bold; font-size: 12px; margin-bottom: 3px; display: block; text-transform: uppercase; }
            .alert-detail ul { margin-top: 4px; margin-bottom: 0; padding-left: 20px; }
            
            .ttd-container { width: 100%; margin-top: 40px; page-break-inside: avoid; }
            .ttd-box { width: 40%; float: right; text-align: center; }
            
            .page-break { page-break-before: always; }
        </style>
    </head>

    <body>
        <div class="header">
            <h2>LAPORAN PERKEMBANGAN SISWA</h2>
            <p>Tahun Ajaran: {{ $currentTa }}</p>
        </div>

        <table class="info-table">
            <tbody>
                <tr>
                    <td class="info-label">Nama Siswa</td>
                    <td class="info-value">: {{ $record->nama_siswa }}</td>

                    <td class="info-label">Tanggal Pemeriksaan</td>
                    <td class="info-value">: 
                        {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="info-label">Kelas / Kelompok</td>
                    <td class="info-value">: {{ $record->kelas ?: '-' }}</td>

                    <td class="info-label">Kelompok Usia</td>
                    <td class="info-value">: {{ $record->kelompok_usia }}</td>
                </tr>
            </tbody>
        </table>

        @foreach($indikatorDefinisinya as $domain => $items)
        <div class="domain-section">
            <h4 class="domain-title">
                {{ strtoupper(str_replace('_', ' ', $domain)) }}
            </h4>
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Indikator Perkembangan</th>
                        <th width="15%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $indikator)
                        @php
                            $val = $record->detail_indikator["indikator_{$indikator->id}"] ?? '-';
                        @endphp
                        <tr>
                            <td>{{ $indikator->indikator }}</td>
                            <td style="text-align:center;">
                                @if($val === 'yes')
                                    <span style="font-weight:bold;">Ya</span>
                                @elseif($val === 'no')
                                    <span style="font-weight:bold; text-decoration: underline;">Tidak</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- TABEL RIWAYAT TREN PERKEMBANGAN (PENGGANTI GRAFIK) --}}
        @php
            // Ambil riwayat yang tanggalnya HANYA SEBELUM ATAU SAMA DENGAN record yang sedang dicetak
            $allHistory = \App\Models\Perkembangan::where('nama_siswa', $record->nama_siswa)
                ->where('created_at', '<=', $record->created_at) // <-- INI KUNCI FOOL-PROOF NYA
                ->orderBy('created_at', 'desc')
                ->get();

            // Kelompokkan berdasarkan Tahun-Bulan, ambil yang paling akhir di tiap bulannya, lalu ambil 3 teratas
            $latestHistory = $allHistory->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('Y-m');
            })->map(function ($group) {
                return $group->first(); 
            })->values()->take(3); 

            // Index 0 = Record yang sedang dicetak (Bulan Ini)
            // Index 1 = Bulan Lalu
            // Index 2 = 2 Bulan Lalu
            $bulanIni = $latestHistory->get(0);
            $bulanLalu = $latestHistory->get(1);
            $duaBulanLalu = $latestHistory->get(2);

            // Fungsi helper untuk format tanggal
            $formatDate = function($model) {
                return $model ? \Carbon\Carbon::parse($model->created_at)->translatedFormat('M Y') : '-';
            };

            // Fungsi helper untuk ambil nilai
            $getVal = function($model, $field) {
                return $model ? round($model->$field) : '-';
            };

            // Fungsi helper untuk indikator tren
            $getTrend = function($now, $prev) {
                if ($now === '-' || $prev === '-') return '-';
                if ($now > $prev) return '<span style="color: #10b981; font-weight: bold;">&#9650; Naik</span>';
                if ($now < $prev) return '<span style="color: #ef4444; font-weight: bold;">&#9660; Turun</span>';
                return '<span style="color: #6b7280; font-weight: bold;">&#9644; Tetap</span>';
            };
        @endphp

        <div style="margin-top: 25px; margin-bottom: 20px; page-break-inside: avoid;">
            <h3 style="text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px; margin-top: 0;">
                Riwayat Tren Perkembangan
            </h3>
            <p style="font-size: 11px; margin-top: 3px; margin-bottom: 10px; font-style: italic;">
                Perbandingan nilai siswa dalam 3 periode evaluasi terakhir.
            </p>

            <table class="score-table" style="text-align: center;">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 28%;">Domain Perkembangan</th>
                        <th style="width: 18%;">{{ $formatDate($duaBulanLalu) }}</th>
                        <th style="width: 18%;">{{ $formatDate($bulanLalu) }}</th>
                        <th style="width: 18%; background-color: #e5e7eb;">{{ $formatDate($bulanIni) }}<br><small>(Saat Ini)</small></th>
                        <th style="width: 18%;">Tren</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Motorik Kasar -->
                    <tr>
                        <td style="text-align: left; font-weight: bold; background-color: #f9fafb;">Motorik Kasar</td>
                        <td>{{ $getVal($duaBulanLalu, 'nilai_motorik_kasar') }}</td>
                        <td>{{ $getVal($bulanLalu, 'nilai_motorik_kasar') }}</td>
                        <td style="font-weight: bold; background-color: #f3f4f6;">{{ $getVal($bulanIni, 'nilai_motorik_kasar') }}</td>
                        <td>{!! $getTrend($getVal($bulanIni, 'nilai_motorik_kasar'), $getVal($bulanLalu, 'nilai_motorik_kasar')) !!}</td>
                    </tr>
                    
                    <!-- Motorik Halus -->
                    <tr>
                        <td style="text-align: left; font-weight: bold; background-color: #f9fafb;">Motorik Halus</td>
                        <td>{{ $getVal($duaBulanLalu, 'nilai_motorik_halus') }}</td>
                        <td>{{ $getVal($bulanLalu, 'nilai_motorik_halus') }}</td>
                        <td style="font-weight: bold; background-color: #f3f4f6;">{{ $getVal($bulanIni, 'nilai_motorik_halus') }}</td>
                        <td>{!! $getTrend($getVal($bulanIni, 'nilai_motorik_halus'), $getVal($bulanLalu, 'nilai_motorik_halus')) !!}</td>
                    </tr>

                    <!-- Bahasa -->
                    <tr>
                        <td style="text-align: left; font-weight: bold; background-color: #f9fafb;">Bahasa</td>
                        <td>{{ $getVal($duaBulanLalu, 'nilai_bahasa') }}</td>
                        <td>{{ $getVal($bulanLalu, 'nilai_bahasa') }}</td>
                        <td style="font-weight: bold; background-color: #f3f4f6;">{{ $getVal($bulanIni, 'nilai_bahasa') }}</td>
                        <td>{!! $getTrend($getVal($bulanIni, 'nilai_bahasa'), $getVal($bulanLalu, 'nilai_bahasa')) !!}</td>
                    </tr>

                    <!-- Sosial Kemandirian -->
                    <tr>
                        <td style="text-align: left; font-weight: bold; background-color: #f9fafb;">Sosial Kemandirian</td>
                        <td>{{ $getVal($duaBulanLalu, 'nilai_sosial_kemandirian') }}</td>
                        <td>{{ $getVal($bulanLalu, 'nilai_sosial_kemandirian') }}</td>
                        <td style="font-weight: bold; background-color: #f3f4f6;">{{ $getVal($bulanIni, 'nilai_sosial_kemandirian') }}</td>
                        <td>{!! $getTrend($getVal($bulanIni, 'nilai_sosial_kemandirian'), $getVal($bulanLalu, 'nilai_sosial_kemandirian')) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="page-break">
            <h3 style="text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px; margin-top: 0;">Ringkasan Penilaian & Kesimpulan</h3>
            
            <table class="score-table">
                <thead>
                    <tr>
                        <th>Domain Perkembangan</th>
                        <th style="width: 150px;">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Motorik Halus</td>
                        <td style="text-align: center; font-weight: bold;">{{ round($record->nilai_motorik_halus) }} / 100</td>
                    </tr>
                    <tr>
                        <td>Motorik Kasar</td>
                        <td style="text-align: center; font-weight: bold;">{{ round($record->nilai_motorik_kasar) }} / 100</td>
                    </tr>
                    <tr>
                        <td>Bahasa</td>
                        <td style="text-align: center; font-weight: bold;">{{ round($record->nilai_bahasa) }} / 100</td>
                    </tr>
                    <tr>
                        <td>Sosial Kemandirian</td>
                        <td style="text-align: center; font-weight: bold;">{{ round($record->nilai_social_kemandirian ?? $record->nilai_sosial_kemandirian) }} / 100</td>
                    </tr>
                </tbody>
            </table>

            @php
                // 1. Ekstrak angka dari string kelompok_usia
                $currentUsiaStr = $record->kelompok_usia;
                preg_match('/\d+/', $currentUsiaStr, $matches);
                $currentUsiaInt = isset($matches[0]) ? (int)$matches[0] : 0;
                
                // 2. Logika level usia selanjutnya
                $nextUsiaInt = $currentUsiaInt + 1;
                $nextUsiaStr = $nextUsiaInt <= 6 ? $nextUsiaInt . ' Tahun' : null;

                // 3. Susun data domain untuk kalkulasi kesimpulan utama & detail rekom
                $domainsList = [
                    'motorik_kasar'      => ['Motorik Kasar', 'fisik motorik'],
                    'motorik_halus'      => ['Motorik Halus', 'fisik motorik'],
                    'bahasa'             => ['Bahasa', 'bahasa'],
                    'sosial_kemandirian' => ['Sosial Kemandirian', 'sosial kemandirian'],
                ];

                // Cek kelengkapan pengisian kuesioner
                $totalTargetIndikator = \App\Models\DomainPerkembangan::where('kelompok_usia', $record->kelompok_usia)->count();
                $jumlahTerjawab = is_array($record->detail_indikator) ? count($record->detail_indikator) : 0;
                $dataLengkap = $jumlahTerjawab >= $totalTargetIndikator;

                // Variabel counter untuk menentukan Kesimpulan Utama Sistem
                $countPenyimpangan = 0;
                $countMeragukan = 0;

                if ($dataLengkap) {
                    foreach ($domainsList as $col => $d) {
                        $sc = $record->{"nilai_{$col}"};
                        if (!is_null($sc)) {
                            if ($sc < 60) {
                                $countPenyimpangan++;
                            } elseif ($sc < 80) {
                                $countMeragukan++;
                            }
                        }
                    }
                }
            @endphp

            @if (!$dataLengkap)
                <div class="box-conclusion">
                    <span class="box-conclusion-title">Kesimpulan Sistem</span>
                    <p style="margin: 0; font-style: italic;">Data kuesioner belum terisi sepenuhnya. Kesimpulan utama tidak dapat ditentukan.</p>
                </div>
            @else
                <div class="box-conclusion">
                    <span class="box-conclusion-title">Kesimpulan Sistem</span>
                    <p style="margin: 0; font-size: 13px; font-weight: bold; line-height: 1.5;">
                        @if ($countPenyimpangan >= 1)
                            STATUS: PERKEMBANGAN ANAK PENGALAMI PENYIMPANGAN<br>
                            <span style="font-weight: normal; font-size: 12px;">
                                Dianjurkan rujukan ke Puskesmas atau fasilitas kesehatan rujukan untuk pemeriksaan lanjutan.
                            </span>
                        @elseif ($countMeragukan == 1)
                            STATUS: PERKEMBANGAN ANAK KURANG SESUAI<br>
                            <span style="font-weight: normal; font-size: 12px;">
                                Dianjurkan stimulasi terarah secara intensif dan lakukan penilaian ulang 1–3 bulan ke depan terhadap domain perkembangan tersebut.
                            </span>
                        @elseif ($countMeragukan > 1)
                            {{-- Antisipasi jika ada lebih dari 1 domain meragukan, mengikuti prioritas indikasi rujukan faskes --}}
                            STATUS: PERKEMBANGAN ANAK KURANG SESUAI (&ge; 2 Domain)<br>
                            <span style="font-weight: normal; font-size: 12px;">
                                Dianjurkan konsultasi intensif, pemeriksaan berkala, atau rujukan ke fasilitas kesehatan jika tidak ada kemajuan dalam 1 bulan.
                            </span>
                        @else
                            STATUS: PERKEMBANGAN SISWA SESUAI USIA<br>
                            <span style="font-weight: normal; font-size: 12px;">
                                Perkembangan Anak Sesuai dengan Target Perkembangan. Anak dinyatakan berkembang sesuai usia. Lanjutkan stimulasi rutin sesuai tahapan umur anak.
                            </span>
                        @endif
                    </p>
                </div>

                <h4 style="text-transform: uppercase; margin-top: 20px; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 3px;">Detail Rekomendasi per Domain Perkembangan</h4>
                
                @foreach ($domainsList as $column => $data)
                    @php
                        $label = $data[0];
                        $jenisRekomDB = $data[1];
                        $score = $record->{"nilai_{$column}"};

                        if (is_null($score)) continue;
                    @endphp

                    {{-- DOMAIN KATEGORI PENYIMPANGAN (< 60) --}}
                    @if ($score < 60)
                        <div class="alert-detail">
                            <span class="alert-title">{{ $label }} &mdash; Penyimpangan</span>
                            <p style="margin:0; font-size: 11px; font-style: italic;">(Tidak menampilkan daftar stimulasi. Domain ini membutuhkan penanganan klinis / rujukan medis segera).</p>
                        </div>

                    {{-- DOMAIN KATEGORI MERAGUKAN (60-79) ATAU SESUAI (>=80) --}}
                    @else
                        @php
                            if ($score < 80) {
                                $statusTeks = "Meragukan (Butuh Stimulasi)";
                                $targetUsia = $currentUsiaStr; // Gunakan usia saat ini
                            } else {
                                $statusTeks = "Sesuai Perkembangan";
                                $targetUsia = $nextUsiaStr; // Lompat ke level usia berikutnya
                            }

                            $rekomendasiHtml = '';
                            if ($targetUsia) {
                                $rekomendasiDb = \App\Models\Rekomendasi::where('jenis_rekomendasi', $jenisRekomDB)
                                                                ->where('kelompok_usia', $targetUsia)
                                                                ->pluck('nama_rekomendasi')
                                                                ->toArray();

                                if (count($rekomendasiDb) > 0) {
                                    $rekomendasiHtml = "<ul><li>" . implode("</li><li>", $rekomendasiDb) . "</li></ul>";
                                } else {
                                    $rekomendasiHtml = "<p style='margin: 4px 0 0; font-style: italic; color: #555;'>(Belum ada data rekomendasi tertulis di database untuk tahap ini)</p>";
                                }
                            } else {
                                $rekomendasiHtml = "<p style='margin: 4px 0 0; font-weight: bold;'>Anak telah mencapai evaluasi batas usia maksimal di sistem (6 Tahun).</p>";
                            }
                        @endphp

                        <div class="alert-detail">
                            <span class="alert-title">{{ $label }} &mdash; {{ $statusTeks }}</span>
                            @if($targetUsia)
                                <strong style="font-size: 11px; display: block; margin-top: 3px;">Daftar Stimulasi Sasaran ({{ $targetUsia }}):</strong>
                            @endif
                            {!! $rekomendasiHtml !!}
                        </div>
                    @endif
                @endforeach
            @endif
            
            <div class="ttd-container">
                <div class="ttd-box">
                    <p>Mengetahui,</p>
                    <p style="margin-bottom: 50px;">Guru Kelas / Pengisi</p>
                    <p><strong>{{ $perkembangan->pengisi ?? $record->pengisi ?? '( ................................... )' }}</strong></p>
                </div>
            </div>
        </div> 
    </body>
</html>
