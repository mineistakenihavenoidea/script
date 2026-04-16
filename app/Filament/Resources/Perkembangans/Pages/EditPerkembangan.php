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
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['pengisi'] = auth()->user()->nama_guru;
        
        $kelompokUsiaDb = $data['kelompok_usia'] ?? null;

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
                    $jawaban = $data["indikator_$id"] ?? null;

                    if ($jawaban === 'yes') {
                        $yes++;
                    }

                    if($jawaban !== null){
                        $detail_indikator["indikator_$id"] = $jawaban;
                    }

                    unset($data["indikator_$id"]);
                }

                $data["nilai_$columnName"] = ($yes / $total) * 100;
            } else {
                $data["nilai_$columnName"] = 0;
            }
        }

        $data['detail_indikator'] = $detail_indikator;

        return $data;
    }

}
