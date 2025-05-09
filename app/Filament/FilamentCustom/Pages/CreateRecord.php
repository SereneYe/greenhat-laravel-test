<?php

namespace App\Filament\FilamentCustom\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;

class CreateRecord extends BaseCreateRecord
{
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

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->action('create')
            ->keyBindings(['mod+s']);
    }

}
