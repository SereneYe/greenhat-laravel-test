<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Closure;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\CreateAction as BaseAction;
use Illuminate\Contracts\Support\Htmlable;

class CreateAction extends BaseAction
{
    protected bool|Closure $isLabelHidden = true;

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
}
