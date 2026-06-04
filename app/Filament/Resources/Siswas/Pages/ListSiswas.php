<?php

namespace App\Filament\Resources\Siswas\Pages;

use App\Filament\Resources\Siswas\SiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
    

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('toggleActiveTa')
                ->label(fn () => $this->onlyActiveTa ? 'Siswa Aktif' : 'Semua Data')
                ->color(fn () => $this->onlyActiveTa ? 'success' : 'gray')
                ->icon(fn () => $this->onlyActiveTa ? 'heroicon-m-arrows-pointing-out' : 'heroicon-m-arrows-pointing-in')
                ->action(function () {
                    $this->onlyActiveTa = ! $this->onlyActiveTa;
                }),
        ];
    }
    
    public function getMaxContentWidth(): Width | string |null
    {
        return Width::Full;
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

    public bool $onlyActiveTa = true;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->onlyActiveTa) {
            $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
            $currentTaYearOne = ($currentStartYear - 1) . "/{$currentStartYear}";
            $currentTaYearTwo = "{$currentStartYear}/" . ($currentStartYear + 1);

            $activeTa = [$currentTaYearOne, $currentTaYearTwo];

            $query->whereIn('ta_masuk', $activeTa);
        }

        return $query->orderByDesc('id');
    }
}
