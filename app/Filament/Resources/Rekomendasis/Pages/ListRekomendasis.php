<?php

namespace App\Filament\Resources\Rekomendasis\Pages;

use App\Filament\Resources\Rekomendasis\RekomendasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRekomendasis extends ListRecords
{
    protected static string $resource = RekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
