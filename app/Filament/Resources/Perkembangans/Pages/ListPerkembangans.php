<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use App\Filament\Resources\Perkembangans\Widgets\PerkembanganStatsOverview;

class ListPerkembangans extends ListRecords
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getMaxContentWidth(): Width | string |null
    {
        return Width::Full;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PerkembanganStatsOverview::class,
        ];
    }
}
