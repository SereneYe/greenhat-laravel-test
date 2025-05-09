<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\FilamentCustom\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'View';

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getResource()::getFormViewLayout($this->mainSchema(), []))
            ->model($this->getRecord())
            ->statePath($this->getFormStatePath())
            ->operation('view')
            ->statePath($this->getFormStatePath())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }

    protected function mainSchema(): array
    {
        return [
            Section::make('User Information')
                ->relationship('user')
                ->columns()
                ->schema([
                    TextInput::make('firstName')
                        ->label('First Name')
                        ->required(),

                    TextInput::make('lastName')
                        ->label('Last Name')
                        ->required(),

                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true),
                ]),

            Section::make('Employee Information')
                ->columns()
                ->schema([
                    TextInput::make('role'),
                ]),
        ];
    }
}
