<?php

namespace App\Filament\Resources;

use App\Filament\FilamentCustom\Tables\Actions\ViewAction;
use App\Filament\FilamentCustom\Tables\Columns\DateTimeColumn;
use App\Filament\Resources\EmployeeFeedbackResource\Pages;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Employee\Models\EmployeeFeedback;

class EmployeeFeedbackResource extends Resource
{
    protected static ?string $model = EmployeeFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Staff';

    protected static ?string $navigationLabel = 'Feedback';

    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('email'),
                Textarea::make('comment')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                DateTimeColumn::make('created_at')
                    ->label('Submitted At')
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeFeedback::route('/'),
            'edit' => Pages\EditEmployeeFeedback::route('/{record}/edit'),
        ];
    }
}
