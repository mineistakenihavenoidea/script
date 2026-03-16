<?php

namespace App\Filament\Resources\DomainPerkembangans\Pages;

use App\Filament\Resources\DomainPerkembangans\DomainPerkembanganResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDomainPerkembangan extends ViewRecord
{
    protected static string $resource = DomainPerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
