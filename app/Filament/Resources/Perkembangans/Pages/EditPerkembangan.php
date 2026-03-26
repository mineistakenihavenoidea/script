<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\DomainPerkembangan;
use App\Models\Siswa;
use Carbon\Carbon;

class EditPerkembangan extends EditRecord
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $siswa = Siswa::where('nama_siswa', $data['nama_siswa'] ?? '')->first();
        $kelompokUsiaDb = '';

        if ($siswa && $siswa->tanggal_lahir) {
            $umur = Carbon::parse($siswa->tanggal_lahir)->age;
            if ($umur <= 4) $kelompokUsiaDb = '4 Tahun';
            elseif ($umur > 4 && $umur < 6) $kelompokUsiaDb = '5 Tahun';
            elseif ($umur >= 6) $kelompokUsiaDb = '6 Tahun';
        }

        $domainsMap = [
            'motorik_halus' => 'motorik halus',
            'motorik_kasar' => 'motorik kasar',
            'bahasa' => 'bahasa',
            'sosial_kemandirian' => 'sosial kemandirian',
        ];

        $detail_indikator =[];

        foreach ($domainsMap as $columnName => $domainName) {
            $indicators = DomainPerkembangan::where('domain', $domainName)
                ->where('kelompok_usia', $kelompokUsiaDb)
                ->pluck('id');

            $yes = 0;
            $total = count($indicators);
        
            if ($total > 0) {
                foreach ($indicators as $id) {
                    if (($data["indikator_$id"] ?? null) === 'yes') {
                        $yes++;
                    }

                    if($data !== null){
                        $detailIndikator["indikator_$id"] = $data;
                    }

                    unset($data["indikator_$id"]);
                }

                $data["nilai_$columnName"] = ($yes / $total) * 100;
            } else {
                $data["nilai_$columnName"] = 0;
            }
        }

        return $data;
    }

}
