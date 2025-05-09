<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Tables\Actions\RestoreAction as BaseAction;

class RestoreAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getColor(): string
    {
        return 'success';
    }

    public function getTooltip(): string
    {
        return 'Restore';
    }
}
