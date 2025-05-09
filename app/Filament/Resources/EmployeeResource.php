<?php

namespace App\Filament\Resources;

use App\Filament\FilamentCustom\Tables\Actions\ViewAction;
use App\Filament\Resources\EmployeeResource\Pages;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Employee\Models\Employee;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Staff';

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getRecordTitleAttribute(): ?string
    {
        return 'userName';
    }

    public static function getFormViewLayout(array $mainSchema, ?array $sidebar = null): array
    {
        $schema = [
            Grid::make()
                ->schema([
                    Section::make()
                        ->schema($mainSchema)
                        ->columns(),
                ])
                ->columns(2)
                ->columnSpan(['lg' => 2]),
        ];

        if (is_null($sidebar)) {
            $sidebar = static::sidebarSchema();
        }

        if (!empty($sidebar)) {
            $schema[] = Grid::make()
                ->schema($sidebar)
                ->columnSpan(['lg' => 1]);
        }

        return [
            Grid::make()
                ->schema($schema)
                ->columns(empty($sidebar) ? 1 : 3),
        ];
    }

    public static function sidebarSchema(): array
    {
        return [
            Section::make('Basic Information')
                ->schema([
                    Placeholder::make('userName')
                        ->label('Name')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->user->name),

                    Placeholder::make('userEmail')
                        ->label('Email')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->user->email),

                    Placeholder::make('employeeRole')
                        ->label('Role')
                        ->inlineLabel()
                        ->content(fn (Employee $record): string => $record->role),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->color('primary')
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(Employee::getRoleOptions()),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewEmployee::class,
            Pages\EditEmployeeProfile::class,
            Pages\ViewEmployeeAuthenticationLogs::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'               => Pages\ListEmployees::route('/'),
            'create'              => Pages\CreateEmployee::route('/create'),
            'view'                => Pages\ViewEmployee::route('/{record}'),
            'edit-profile'        => Pages\EditEmployeeProfile::route('/{record}/profile'),
            'authentication-logs' => Pages\ViewEmployeeAuthenticationLogs::route('/{record}/authentication-logs'),
        ];
    }
}
