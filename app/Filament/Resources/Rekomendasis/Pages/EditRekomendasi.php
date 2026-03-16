<?php

namespace App\Filament\Resources\Rekomendasis\Pages;

use App\Filament\Resources\Rekomendasis\RekomendasiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRekomendasi extends EditRecord
{
    protected static string $resource = RekomendasiResource::class;

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
