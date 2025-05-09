<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Tables\Actions\ViewAction as BaseAction;

class ViewAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return 'View';
    }
}
