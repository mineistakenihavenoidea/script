<?php

namespace App\Filament\Resources\Perkembangans\Pages;

use App\Filament\Resources\Perkembangans\PerkembanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use App\Filament\Resources\Perkembangans\Widgets\PerkembanganStatsOverview;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

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

    public function getTabs(): array
    {
        $tabs = [
            'semua' => Tab::make('Semua Kelas')
        ];

        $kelasList = Siswa::select('kelas')->whereNotNull('kelas')->distinct()->pluck('kelas');

        foreach ($kelasList as $kelas) {
            $tabs[$kelas] = Tab::make($kelas)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('kelas', $kelas));
        }

        return $tabs;
    }
}
