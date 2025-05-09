<?php

namespace App\Filament\FilamentCustom\Tables\Columns;

use App\Filament\FilamentCustom\Tables\Columns\Traits\ColumnColor;
use Closure;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;

class RelationCountColumn extends TextColumn
{
    use ColumnColor;

    protected string|array|bool|Closure|null $color = 'warning';

    protected bool|Closure $isBadge = true;

    protected Alignment|string|Closure|null $alignment = Alignment::Center;
}
