<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getColumns(): int|string|array
    {
        return 6;
    }

    public function getWidgets(): array
    {
        return [
        ];
    }
}
