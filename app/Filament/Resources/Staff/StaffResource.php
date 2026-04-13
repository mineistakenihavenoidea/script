<?php

namespace App\Filament\Resources\Staff;

use App\Filament\Resources\Staff\Pages\CreateStaff;
use App\Filament\Resources\Staff\Pages\EditStaff;
use App\Filament\Resources\Staff\Pages\ListStaff;
use App\Filament\Resources\Staff\Pages\ViewStaff;
use App\Filament\Resources\Staff\Schemas\StaffForm;
use App\Filament\Resources\Staff\Schemas\StaffInfolist;
use App\Filament\Resources\Staff\Tables\StaffTable;
use App\Models\Staff;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static string | UnitEnum | null $navigationGroup = 'Pengurus';

    protected static ?string $navigationLabel = 'Pengurus';

    protected static ?string $recordTitleAttribute = 'nama_guru';

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StaffInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
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
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'view' => ViewStaff::route('/{record}'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->staff()->canCrudPerkembangan()
            || auth()->staff()->canReadPerkembangan();
    }

    public static function canView($record): bool
    {
        return auth()->staff()->canCrudPerkembangan()
            || auth()->staff()->canReadPerkembangan();
    }

    public static function canCreate(): bool
    {
        return auth()->staff()->canCrudPerkembangan();
    }

    public static function canEdit($record): bool
    {
        return auth()->staff()->canCrudPerkembangan();
    }

    public static function canDelete($record): bool
    {
        return auth()->staff()->canCrudPerkembangan();
    }
}
