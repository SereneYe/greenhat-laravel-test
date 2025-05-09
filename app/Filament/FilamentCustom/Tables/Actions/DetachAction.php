<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Tables\Actions\DetachAction as BaseAction;

class DetachAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return 'Detach';
    }
}
