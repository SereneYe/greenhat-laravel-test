<?php

namespace App\Filament\FilamentCustom\Pages\Actions;

use Closure;
use Filament\Actions\CreateAction as BaseAction;
use Filament\Actions\StaticAction;
use Illuminate\Contracts\Support\Htmlable;

class CreateAction extends BaseAction
{
    protected bool | Closure $isLabelHidden = false;

    public function getModalSubmitAction(): ?StaticAction
    {
        $action = static::makeModalAction('submit')
            ->label($this->getModalSubmitActionLabel())
            ->action($this->getLivewireCallMountedActionName())
            ->color(match ($color = $this->getColor()) {
                'gray' => 'primary',
                default => $color,
            });

        if ($this->modalSubmitAction !== null) {
            $action = $this->evaluate($this->modalSubmitAction, ['action' => $action]) ?? $action;
        }

        if ($action === false) {
            return null;
        }

        return $action;
    }

    public function getIcon(): string|Htmlable|null
    {
        return 'heroicon-o-plus';
    }

    public function getLabel(): string|Htmlable|null
    {
        return 'Add';
    }
}
