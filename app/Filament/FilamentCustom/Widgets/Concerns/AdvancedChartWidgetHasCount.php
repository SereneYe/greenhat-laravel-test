<?php

namespace App\Filament\FilamentCustom\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait AdvancedChartWidgetHasCount
{
    public float $previousCount = 0;

    public float $currentCount = 0;

    public ?string $dateColumn = 'created_at';

    protected function getQuery(): ?Builder
    {
        return null;
    }

    protected function previousCount(?string $dateColumn = null): static
    {
        if ($query = $this->getQuery()) {
            $this->previousCount = with($query)->whereBetween(
                $this->dateColumn,
                $this->previousRange($this->filter)
            )->count();
        }

        return $this;
    }

    protected function currentCount(?string $dateColumn = null): static
    {
        if ($query = $this->getQuery()) {
            $this->currentCount = with($query)->whereBetween(
                $this->dateColumn,
                $this->currentRange($this->filter)
            )->count();
        }

        return $this;
    }

    protected function currentRange($range): array
    {
        if ($range == 'today') {
            return [
                now()->today(),
                now(),
            ];
        }

        return [
            now()->subDays($range - 1),
            now(),
        ];
    }

    protected function previousRange($range): array
    {
        if ($range == 'today') {
            return [
                now()->modify('yesterday')->setTime(0, 0),
                now()->subDay(),
            ];
        }

        return [
            now()->subDays(($range - 1) * 2),
            now()->subDays($range - 1),
        ];
    }

    protected function getPercentage(): float
    {
        return round((($this->currentCount - $this->previousCount) / $this->previousCount) * 100, 2);
    }
}
