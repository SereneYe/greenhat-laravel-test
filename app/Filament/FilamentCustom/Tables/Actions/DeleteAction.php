<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Tables\Actions\DeleteAction as BaseAction;

class DeleteAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return 'Delete';
    }
}
