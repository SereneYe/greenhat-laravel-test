<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\FilamentCustom\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\EmployeeResource\RelationManagers\EmployeeLoginsRelationManager;
use Filament\Forms\Form;

class ViewEmployeeAuthenticationLogs extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $navigationLabel = 'Auth Logs';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public function form(Form $form): Form
    {
        return $form
            ->model($this->getRecord())
            ->statePath($this->getFormStatePath())
            ->operation('edit')
            ->statePath($this->getFormStatePath())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }

    public function getRelationManagers(): array
    {
        return [
            EmployeeLoginsRelationManager::class,
        ];
    }
}
