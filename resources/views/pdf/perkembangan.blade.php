@php
    $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
    $currentTa = "{$currentStartYear}/" . ($currentStartYear + 1);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Perkembangan Siswa</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 12px; color: #555; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px; vertical-allign: top; border: none; }
        .info-table {width: 150px; font-weight: bold;}
        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .score-table th, .score-table td { border: 1px solid #999; padding: 8px; text-align: left; }
        .score-table th { background-color: #f3f4f6; font-weight: bold; text-align: center; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 12px; border: 1px solid; }
        .alert-red { background-color: #fee2e2; border-color: #f87171; color: #991b1b;}
        .alert-yellow { background-color: #fef9c3; border-color: #facc15; color: #854d0e;}
        .alert-green { background-color: #dcfce3; border-color: #4ade80; color: #166534;}
        .alert-title { font-weight: bold; font-size: 14px;margin-bottom: 4px; display: block; }
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { width: 40%; float: right; text-align: center; }
        .domain-row { background-color: #fafafa; font-weight: bold; }
        .footer { margin-top: 30px; }
        .signature { float: right; width: 200px; text-align: center; }
        .kesimpulan { border: 1px solid #444; padding: 10px; margin-top: 10px; background: #f9f9f9; }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN PERKEMBANGAN SISWA</h2>
        <p>Tahun Ajaran: {{ $currentTa }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Siswa</td>
            <td>: <strong>{{ $record->nama_siswa }}</strong></td>
            <td class="info-label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Kelas / Kelompok</td>
            <td>: {{ $record->kelas ?? '-' }}</td>
            <td class="info-label">Kelompok Usia</td>
            <td>: {{ $record->kelompok_usia }}</td>
        </tr>
    </table>

    <table class="score-table">
        <thead>
            <tr>
                <th>Indikator Perkembangan</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($indikatorDefinisinya as $domain => $items)
                <tr class="domain-row">
                    <td colspan="2">{{ strtoupper(str_replace('_', ' ', $domain)) }}</td>
                </tr>
                @foreach($items as $indikator)
                    @php 
                        $val = $record->detail_indikator["indikator_{$indikator->id}"] ?? '-';
                    @endphp
                    <tr>
                        <td>{{ $indikator->indikator }}</td>
                        <td style="text-align: center; color: {{ $val == 'yes' ? 'green' : 'red' }}">
                            {{ $val == 'yes' ? 'Ya' : ($val == 'no' ? 'Tidak' : '-') }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

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
                <td style="text-align: center;">{{ round($record->nilai_motorik_halus) }} / 100</td>
            </tr>
            <tr>
                <td>Motorik Kasar</td>
                <td style="text-align: center;">{{ round($record->nilai_motorik_kasar) }} / 100</td>
            </tr>
            <tr>
                <td>Bahasa</td>
                <td style="text-align: center;">{{ round($record->nilai_bahasa) }} / 100</td>
            </tr>
            <tr>
                <td>Sosial Kemandirian</td>
                <td style="text-align: center;">{{ round($record->nilai_sosial_kemandirian) }} / 100</td>
            </tr>
        </tbody>
    </table>

    @php
        $domains = [
            'motorik_halus' => 'Motorik Halus',
            'motorik_kasar' => 'Motorik Kasar',
            'bahasa' => 'Bahasa',
            'sosial_kemandirian' => 'Sosial Kemandirian',
        ];

        $butuhRujukan = [];
        $butuhStimulasi = [];

        foreach ($domains as $name => $score) {
            if (is_null($score)) continue;
            if ($score < 60) {
                $butuhRujukan[] = $name;
            } elseif ($score < 80) {
                $butuhStimulasi[] = $name;
            }
        }
    @endphp

    @if(count($butuhRujukan) > 0)
        <div class="alert alert-red">
            <span class="alert-title">SISWA MEMBUTUHKAN RUJUKAN KHUSUS:</span>
            Terdapat perkembangan yang tidak sesuai pada domain: <strong>{{ implode(', ', $butuhRujukan) }}</strong>. Disarankan untuk segera melakukan konsultasi dengan profesional atau psikolog anak.
        </div>
    @endif

    @if(count($butuhStimulasi) > 0)
        <div class="alert alert-yellow">
            <span class="alert-title">SISWA MEMBUTUHKAN STIMULASI:</span>
            Terdapat perkembangan yang kurang optimal. Disarankan untuk memberikan stimulasi tambahan sebagai berikut:
            <ul style="margin: 5px 0 0; padding-left: 20px;">
                @foreach($butuhStimulasi as $jenis)
                    @php
                        // Opsional: Tarik data rekomendasi dari DB kalau ada
                        $rekom = \App\Models\Rekomendasi::where('jenis_rekomendasi', strtolower($jenis))->pluck('nama_rekomendasi')->toArray();
                        $teks = count($rekom) > 0 ? implode('; ', $rekom) : '(Dibutuhkan stimulasi intensif oleh guru/orang tua)';
                    @endphp
                    <li><strong>{{ $jenis }}:</strong> {{ $teks }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(count($butuhRujukan) === 0 && count($butuhStimulasi) === 0)
        <div class="alert alert-green">
            <span class="alert-title">PERKEMBANGAN SISWA SESUAI:</span>
            Siswa menunjukkan perkembangan yang sesuai dengan usianya pada semua domain. Tetap berikan stimulasi yang baik untuk mendukung perkembangan yang optimal.
        </div>
    @endif

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p style="margin-bottom: 60px;">Guru Kelas / Pengisi</p>
            <p><strong>{{ $perkembangan->pengisi ?? '( ................................... )' }}</strong></p>
        </div>
    </div>

</body>
</html>
