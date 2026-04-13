<?php

namespace App\Filament\Resources\Perkembangans;

use App\Filament\Resources\Perkembangans\Pages\CreatePerkembangan;
use App\Filament\Resources\Perkembangans\Pages\EditPerkembangan;
use App\Filament\Resources\Perkembangans\Pages\ListPerkembangans;
use App\Filament\Resources\Perkembangans\Pages\ViewPerkembangan;
use App\Filament\Resources\Perkembangans\Schemas\PerkembanganForm;
use App\Filament\Resources\Perkembangans\Schemas\PerkembanganInfolist;
use App\Filament\Resources\Perkembangans\Tables\PerkembangansTable;
use App\Models\Perkembangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\Perkembangans\Widgets\PerkembanganStatsOverview;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Siswa;
use App\Models\Staff;

class PerkembanganResource extends Resource
{
    protected static ?string $model = Perkembangan::class;

    protected static string | UnitEnum | null $navigationGroup = 'Siswa';

    protected static ?string $navigationLabel = 'Perkembangan';

    protected static ?string $recordTitleAttribute = 'nama_siswa';

    public static function form(Schema $schema): Schema
    {
        return PerkembanganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PerkembanganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerkembangansTable::configure($table);
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
            'index' => ListPerkembangans::route('/'),
            'create' => CreatePerkembangan::route('/create'),
            'view' => ViewPerkembangan::route('/{record}'),
            'edit' => EditPerkembangan::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PerkembanganStatsOverview::class,
        ];
    }

}
