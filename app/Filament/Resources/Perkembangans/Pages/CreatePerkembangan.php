<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\DomainPerkembangan;

class CreatePerkembangan extends CreateRecord
{
    protected static string $resource = PerkembanganResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $domains = [
            'motorik_halus',
            'motorik_kasar',
            'bahasa',
            'sosial_kemandirian',
        ];

        foreach ($domains as $domain) {

            $indicators = DomainPerkembangan::where('domain', $domain)->pluck('id');
            $yes = 0;
            $total = count($indicators);
        
            if ($total >0) {
                foreach ($indicators as $id) {
                    if (($data["indikator_$id"] ?? null) === 'yes') {
                        $yes++;
                    }

                    unset($data["indikator_$id"]);
                }

                $data["nilai_$domain"] = ($yes / $total) * 100;
            } else {
                $data["nilai_$domain"] = 0;
            }
        }
        return $data;
    }
}