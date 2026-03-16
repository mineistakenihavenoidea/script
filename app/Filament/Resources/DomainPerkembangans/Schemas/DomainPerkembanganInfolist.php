<?php

namespace App\Filament\Resources\DomainPerkembangans\Schemas;

use App\Models\DomainPerkembangan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DomainPerkembanganInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('domain')
                    ->placeholder('-'),
                TextEntry::make('kelompok_usia')
                    ->placeholder('-'),
                TextEntry::make('indikator')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (DomainPerkembangan $record): bool => $record->trashed()),
            ]);
    }
}
