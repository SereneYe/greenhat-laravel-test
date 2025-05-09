<?php

namespace App\Filament\FilamentCustom\Filters;

use Closure;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Arrayable;

class VisibilityFilter extends SelectFilter
{
    protected array | Arrayable | string | Closure | null $options = [
        'draft' => 'Draft',
        'public' => 'Public',
        'private' => 'Private',
    ];
}
