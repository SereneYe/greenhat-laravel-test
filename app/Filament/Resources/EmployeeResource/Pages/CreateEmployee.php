<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\FilamentCustom\Pages\CreateRecord;
use App\Filament\Resources\EmployeeResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Modules\Employee\Models\Employee;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->relationship('user')
                ->schema([
                    Section::make('User Information')
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

                    Section::make('Security')
                        ->columns()
                        ->schema([
                            TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->dehydrated(fn ($state) => filled($state))
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->required(fn (string $context): bool => $context === 'create'),
                        ]),
                ]),

            Section::make('Employee Profile')
                ->columns()
                ->schema([
                    Select::make('role')
                        ->options(Employee::getRoleOptions())
                        ->required(),
                ]),
        ];
    }
}
