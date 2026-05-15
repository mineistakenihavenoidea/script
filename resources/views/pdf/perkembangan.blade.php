<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perkembangan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .main-table th { background-color: #f2f2f2; }
        .domain-row { background-color: #fafafa; font-weight: bold; }
        .footer { margin-top: 30px; }
        .signature { float: right; width: 200px; text-align: center; }
        .kesimpulan { border: 1px solid #444; padding: 10px; margin-top: 10px; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PERKEMBANGAN SISWA</h2>
        <p>Bulan: {{ $record->created_at?->translatedFormat('F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Siswa</td><td width="35%">: <strong>{{ $record->nama_siswa }}</strong></td>
            <td width="15%">Kelas</td><td width="35%">: {{ $record->kelas }}</td>
        </tr>
        <tr>
            <td>Guru</td><td>: {{ $record->nama_guru }}</td>
            <td>Usia</td><td>: {{ $record->kelompok_usia }}</td>
        </tr>
    </table>

    <table class="main-table">
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

    <div class="kesimpulan">
        <strong>Kesimpulan Akhir:</strong><br>
        Status: {{ $record->status_kesimpulan }}
    </div>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ now()->format('d/m/Y') }}</p>
            <br><br><br>
            <p>( {{ $record->nama_guru }} )</p>
            <p>Guru Pendamping</p>
        </div>
    </div>
</body>
</html>
