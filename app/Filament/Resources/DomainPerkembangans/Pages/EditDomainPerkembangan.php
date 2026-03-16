<?php

namespace App\Filament\Resources\DomainPerkembangans\Pages;

use App\Filament\Resources\DomainPerkembangans\DomainPerkembanganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDomainPerkembangan extends EditRecord
{
    protected static string $resource = DomainPerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
