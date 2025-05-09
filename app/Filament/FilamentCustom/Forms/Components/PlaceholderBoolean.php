<?php

namespace App\Filament\FilamentCustom\Forms\Components;

use Closure;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PlaceholderBoolean extends Placeholder
{
    protected bool | Closure | null $hasInlineLabel = true;

    /**
     * @return mixed
     */
    public function getContent(): mixed
    {
        $value = is_null($this->getState()) ? $this->getRecord()?->{$this->name} : $this->getState();

        if ($value) {
            return (new HtmlString(Blade::render(
                '<x-heroicon-o-check-circle style="--c-400:var(--success-400);--c-500:var(--success-500);" class="w-6 h-6 text-custom-500" />'
            )));
        }

        return (new HtmlString(Blade::render(
            '<x-heroicon-o-x-circle class="w-6 h-6 text-danger-600" />'
        )));
    }
}
