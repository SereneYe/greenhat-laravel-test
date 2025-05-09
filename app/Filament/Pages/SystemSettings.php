<?php

namespace App\Filament\Pages;

use DateTimeZone;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class SystemSettings extends SettingsPage
{
    protected static string $settings = \Modules\Base\Settings\GeneralSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('timezone')
                    ->options(
                        collect(DateTimeZone::listIdentifiers())
                            ->mapWithKeys(fn ($timezone) => [$timezone => $timezone])
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),
            ]);
    }
}
