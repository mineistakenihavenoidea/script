<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPerkembangans extends ListRecords
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
