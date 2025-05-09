<?php

namespace App\Filament\FilamentCustom\Pages\Actions;

use Closure;
use Filament\Actions\DeleteAction as BaseAction;

class DeleteAction extends BaseAction
{
    protected bool | Closure $isLabelHidden = true;

    public function getIcon(): string
    {
        return 'heroicon-o-trash';
    }
}
