<?php

namespace App\Filament\FilamentCustom\RelationManagers;


use App\Filament\FilamentCustom\Pages\EditRecord;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager as BaseActivitylogRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ActivitylogRelationManager extends BaseActivitylogRelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return !(App::make($pageClass) instanceof EditRecord);
    }
}
