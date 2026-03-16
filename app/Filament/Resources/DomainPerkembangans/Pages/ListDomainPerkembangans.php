<?php

namespace App\Filament\Resources\DomainPerkembangans\Pages;

use App\Filament\Resources\DomainPerkembangans\DomainPerkembanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDomainPerkembangans extends ListRecords
{
    protected static string $resource = DomainPerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
