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
use Filament\Forms\Components\Select;

class ListPerkembangans extends ListRecords
{
    protected static string $resource = PerkembanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('Spreadsheet')
                ->label('Export Excel')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->modalHeading('Filter Data')
                ->modalDescription('Filter data yang ingin di export. Biarkan kosong untuk export semua data.')
                ->modalSubmitActionLabel('Download')
                ->form([
                    Select::make('kelas')
                        ->label('Kelas')
                        ->options(function () {
                            return Siswa::distinct('kelas')->pluck('kelas', 'kelas')->toArray();
                        })
                        ->searchable()
                        ->placeholder('Semua'),
                    
                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            'Januari' => 'Januari',
                            'Februari' => 'Februari',
                            'Maret' => 'Maret',
                            'April' => 'April',
                            'Mei' => 'Mei',
                            'Juni' => 'Juni',
                            'Juli' => 'Juli',
                            'Agustus' => 'Agustus',
                            'September' => 'September',
                            'Oktober' => 'Oktober',
                            'November' => 'November',
                            'Desember' => 'Desember',
                        ])
                        ->searchable()
                        ->placeholder('Semua'),

                    Select::make('tahun')
                        ->label('Tahun')
                        ->options(function () {
                            $years = [];
                            $currentYear = now()->year;
                            for ($i = 0; $i < 3; $i++) {
                                $years[$currentYear - $i] = $currentYear - $i;
                            }
                            return $years;
                        })
                        ->searchable()
                        ->placeholder('Semua'),

                    Select::make('cakupan_ta')
                        ->label('Cakupan Data')
                        ->options([
                            'aktif' => 'Data Siswa Aktif',
                            'semua' => 'Semua Data',
                        ])
                        ->default('aktif')
                        ->searchable()
                        ->placeholder('Semua'),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    return Excel::download(new PerkembanganExport($data), 'Data_Perkembangan_Siswa.xlsx');
                }),
            Action::make('toggleLatestPerkembangan')
                ->label(fn () => $this->onlyLatestPerkembangan ? 'Semua Data' : 'Data Terbaru')
                ->color(fn () => $this->onlyLatestPerkembangan ? 'success' : 'gray')
                ->action(function () {
                    $this->onlyLatestPerkembangan = ! $this->onlyLatestPerkembangan;
                }),
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

    protected function getHeaderWidgets(): array
    {
        return [
            PerkembanganStatsOverview::class,
        ];
    }

    public bool $onlyLatestPerkembangan = false;

    public bool $onlyActiveTa = true;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->onlyActiveTa) {
            $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
            $currentTaYearOne = ($currentStartYear - 1) . "/{$currentStartYear}";
            $currentTaYearTwo = "{$currentStartYear}/" . ($currentStartYear + 1);

            $activeTa = [$currentTaYearOne, $currentTaYearTwo];

            $query->whereHas('siswa', function ($q) use ($activeTa) {
                $q->whereIn('ta_masuk', $activeTa);
            });
        }

        if ($this->onlyLatestPerkembangan) {
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
