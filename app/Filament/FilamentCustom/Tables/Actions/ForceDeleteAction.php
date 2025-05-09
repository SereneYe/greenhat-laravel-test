<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Tables\Actions\ForceDeleteAction as BaseAction;

class ForceDeleteAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return 'Permanent Delete';
    }
}
