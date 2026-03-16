<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\DomainPerkembangan;

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

    protected function mutateFormDataBeforeSave(array $data): array
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
            }
        }
        return $data;
    }

}
