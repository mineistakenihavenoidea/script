<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;

class CustomAccountWidget extends Widget
{
    protected static ?int $sort = -3;
    
    protected static bool $isLazy = false;

    /**
     * @var view-string
     */

    protected string $view = 'filament.widgets.custom-account-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Filament::auth()->check();
    }
}
