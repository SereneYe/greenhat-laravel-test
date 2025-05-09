<?php

namespace App\Filament\FilamentCustom\Tables\Columns;

use App\Filament\FilamentCustom\Tables\Columns\Traits\ColumnColor;
use Carbon\Carbon;
use Closure;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class DateTimeColumn extends TextColumn
{
    use ColumnColor;

    protected string|array|bool|Closure|null $color = 'info';

    protected bool|Closure $isBadge = true;

    protected Alignment|string|Closure|null $alignment = Alignment::Center;

    protected string|Htmlable|Closure|null $placeholder = '-';

    protected string $format = 'd-m-Y H:i:s';

    protected bool|Closure $isExpires = false;

    public function format(string $format = 'd-m-Y H:i:s'): static
    {
        $this->format = $format;

        return $this;
    }

    public function expires(bool|Closure $expires = true): static
    {
        $this->isExpires = $expires;

        return $this;
    }

    public function isExpires(): bool
    {
        return (bool) $this->evaluate($this->isExpires);
    }

    protected function formatDate(): void
    {
        if (! strtotime($this->getState())) {
            return;
        }

        if (! ($this->getState() instanceof Carbon)) {
            $this->state(
                Carbon::parse($this->getState())
            );
        }

        $this->formatStateUsing(fn ($state) => $state?->format($this->format));
    }

    public function render(): View
    {
        $this->formatDate();

        if ($this->isExpires()) {
            $this->danger();
        } else {
            $this->success();
        }

        return parent::render();
    }
}
