<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeLoginsRelationManager extends RelationManager
{
    protected static string $relationship = 'authenticationLogs';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('authentication_log.login_at', 'desc')
            ->columns([
                TextColumn::make('login_at')
                    ->badge()
                    ->sortable(),
                IconColumn::make('login_successful'),
                TextColumn::make('ip_address')
                    ->badge()
                    ->color('info'),
                TextColumn::make('user_agent')
                    ->wrap()
                    ->lineClamp(2),
            ]);
    }
}
