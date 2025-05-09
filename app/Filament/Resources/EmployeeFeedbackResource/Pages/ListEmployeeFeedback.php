<?php

namespace App\Filament\Resources\EmployeeFeedbackResource\Pages;

use App\Filament\FilamentCustom\Pages\ListRecords;
use App\Filament\Resources\EmployeeFeedbackResource;

class ListEmployeeFeedback extends ListRecords
{
    protected static string $resource = EmployeeFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
