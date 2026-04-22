<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;
use App\Filament\Resources\Perkembangans\Schemas\PerkembanganInfolist;
use Filament\Actions\Action;

class ViewPerkembangan extends ViewRecord
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('customAction')
                ->label('Click Me')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->action(function () {
                    // Your logic here
                    Notification::make()->title('Clicked!')->success()->send();
                })
                ->requiresConfirmation(), // Optional: confirmation modal
        ];
    }

    public function getTitle(): string
    {
        return "Data Perkembangan {$this->record->nama_siswa}";
    }

    public function infolist(Schema $schema): Schema
    {
        return PerkembanganInfolist::configure($schema);
    }

}
