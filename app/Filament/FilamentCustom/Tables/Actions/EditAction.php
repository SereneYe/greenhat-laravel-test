<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Filament\Actions\StaticAction;
use Filament\Tables\Actions\EditAction as BaseAction;

class EditAction extends BaseAction
{
    public function getLabel(): string
    {
        return '';
    }

    public function getTooltip(): string
    {
        return 'Edit';
    }

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
}
