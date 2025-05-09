<?php

namespace App\Filament\FilamentCustom\RelationManagers;


use App\Filament\FilamentCustom\Pages\EditRecord;
use Filament\Resources\RelationManagers\RelationManager as BaseRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class RelationManager extends BaseRelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return !(App::make($pageClass) instanceof EditRecord);
    }
}
