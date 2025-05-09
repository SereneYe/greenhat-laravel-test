<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\FilamentCustom\Pages\Actions\DeleteAction;
use App\Filament\FilamentCustom\Pages\EditRecord;
use App\Filament\Resources\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
