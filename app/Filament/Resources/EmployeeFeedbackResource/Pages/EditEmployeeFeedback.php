<?php

namespace App\Filament\Resources\EmployeeFeedbackResource\Pages;

use App\Filament\FilamentCustom\Pages\EditRecord;
use App\Filament\Resources\EmployeeFeedbackResource;

class EditEmployeeFeedback extends EditRecord
{
    protected static string $resource = EmployeeFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
