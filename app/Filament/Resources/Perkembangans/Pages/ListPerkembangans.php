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
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Exports\PerkembanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListPerkembangans extends ListRecords
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('Spreadsheet')
                ->label('Konversi Spreadsheet')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->action(function ($record) {
                    // 1. your logic
                    Notification::make()
                        ->title('Generating Spreadsheet...')
                        ->success()
                        ->send();

                    // 2. open PDF in new tab
                    return Excel::download(new PerkembanganExport(), 'Data_Perkembangan_Siswa.xlsx'
                    );
                }),
            Action::make('toggleLatest')
            ->label(fn () => $this->onlyLatest ? 'Semua Data' : 'Data Terbaru')
            ->color(fn () => $this->onlyLatest ? 'success' : 'gray')
            ->action(function () {
                $this->onlyLatest = ! $this->onlyLatest;
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

    protected function getHeaderWidgets(): array
    {
        return [
            PerkembanganStatsOverview::class,
        ];
    }

    public bool $onlyLatest = false;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->onlyLatest) {
            $query->whereIn('id', function ($q) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('perkembangan')
                    ->whereNull('deleted_at')
                    ->groupBy('nama_siswa');
            });
        }

        return $query->orderByDesc('id');
    }
}
