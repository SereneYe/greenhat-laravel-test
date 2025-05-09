<?php

namespace App\Filament\FilamentCustom\Pages;

use Filament\Resources\Pages\ManageRecords as BaseManageRecords;

class ManageRecords extends BaseManageRecords
{
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
