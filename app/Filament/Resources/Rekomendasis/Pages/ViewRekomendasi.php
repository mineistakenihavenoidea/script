<?php

namespace App\Filament\Resources\Rekomendasis\Pages;

use App\Filament\Resources\Rekomendasis\RekomendasiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRekomendasi extends ViewRecord
{
    protected static string $resource = RekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
