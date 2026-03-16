<?php

namespace App\Filament\Resources\Rekomendasis;

use App\Filament\Resources\Rekomendasis\Pages\CreateRekomendasi;
use App\Filament\Resources\Rekomendasis\Pages\EditRekomendasi;
use App\Filament\Resources\Rekomendasis\Pages\ListRekomendasis;
use App\Filament\Resources\Rekomendasis\Pages\ViewRekomendasi;
use App\Filament\Resources\Rekomendasis\Schemas\RekomendasiForm;
use App\Filament\Resources\Rekomendasis\Schemas\RekomendasiInfolist;
use App\Filament\Resources\Rekomendasis\Tables\RekomendasisTable;
use App\Models\Rekomendasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RekomendasiResource extends Resource
{
    protected static ?string $model = Rekomendasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Rekomendasi';

    public static function form(Schema $schema): Schema
    {
        return RekomendasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RekomendasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RekomendasisTable::configure($table);
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
            'index' => ListRekomendasis::route('/'),
            'create' => CreateRekomendasi::route('/create'),
            'view' => ViewRekomendasi::route('/{record}'),
            'edit' => EditRekomendasi::route('/{record}/edit'),
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
