<?php

namespace App\Filament\FilamentCustom\Pages;

use App\Filament\FilamentCustom\Pages\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord as BaseEditRecord;

class EditRecord extends BaseEditRecord
{
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
        ];
    }

    protected function getRedirectUrl(): string
    {
        $resource = parent::getResource();

        if ($resource::hasPage('view')) {
            return $resource::getUrl('view', [
                'record' => $this->getRecord(),
            ]);
        }

        return $resource::getUrl();
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->action('save')
            ->keyBindings(['mod+s']);
    }

}
