<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\FilamentCustom\Pages\ListRecords;
use App\Filament\Resources\UserResource;
use Filament\Actions;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
