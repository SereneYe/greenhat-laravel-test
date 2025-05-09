<?php

namespace App\Filament\FilamentCustom\Pages\Actions;

use Closure;
use Filament\Actions\EditAction as BaseAction;

class EditAction extends BaseAction
{
    protected bool | Closure $isLabelHidden = true;

    public function getIcon(): string
    {
        return 'heroicon-o-pencil-square';
    }

    public function getLabel(): string
    {
        return '';
    }
}
