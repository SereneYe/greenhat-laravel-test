<?php

namespace App\Filament\FilamentCustom\Tables\Actions;

use Closure;
use Filament\Tables\Actions\AttachAction as BaseAction;

class AttachAction extends BaseAction
{
    protected bool | Closure $isRecordSelectPreloaded = true;

    /**
     * @return bool|Closure
     */
    public function getIsRecordSelectPreloaded(): bool|Closure
    {
        return $this->isRecordSelectPreloaded;
    }
}
