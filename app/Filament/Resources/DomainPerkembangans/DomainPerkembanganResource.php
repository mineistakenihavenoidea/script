<?php

namespace App\Filament\Resources\DomainPerkembangans;

use App\Filament\Resources\DomainPerkembangans\Pages\CreateDomainPerkembangan;
use App\Filament\Resources\DomainPerkembangans\Pages\EditDomainPerkembangan;
use App\Filament\Resources\DomainPerkembangans\Pages\ListDomainPerkembangans;
use App\Filament\Resources\DomainPerkembangans\Pages\ViewDomainPerkembangan;
use App\Filament\Resources\DomainPerkembangans\Schemas\DomainPerkembanganForm;
use App\Filament\Resources\DomainPerkembangans\Schemas\DomainPerkembanganInfolist;
use App\Filament\Resources\DomainPerkembangans\Tables\DomainPerkembangansTable;
use App\Models\DomainPerkembangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainPerkembanganResource extends Resource
{
    protected static ?string $model = DomainPerkembangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DomainPerkembangan';

    public static function form(Schema $schema): Schema
    {
        return DomainPerkembanganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DomainPerkembanganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainPerkembangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomainPerkembangans::route('/'),
            'create' => CreateDomainPerkembangan::route('/create'),
            'view' => ViewDomainPerkembangan::route('/{record}'),
            'edit' => EditDomainPerkembangan::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
