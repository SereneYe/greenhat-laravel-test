<?php

namespace App\Filament\FilamentCustom\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AdvancedLineChartWidgetWithCount extends AdvancedChartWidget
{
    use Concerns\AdvancedChartWidgetHasCount;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 2;

    protected static ?string $label = 'Chart';

    public ?string $filter = '7';

    public function render(): View
    {
        $this->currentCount()
            ->previousCount();

        return parent::render();
    }

    public function getHeading(): ?string
    {
        return $this->currentCount;
    }

    public function getBadge(): ?string
    {
        $previousCount = $this->previousCount;
        $currentCount = $this->currentCount;

        if ($previousCount == $currentCount) {
            return null;
        }

        if ($previousCount == 0) {
            return 'No Prior Data';
        }

        return $this->getPercentage().'% '.($previousCount < $currentCount ? 'Increase' : 'Decrease');
    }

    public function getBadgeColor(): ?string
    {
        $previousCount = $this->previousCount;
        $currentCount = $this->currentCount;

        if ($previousCount == 0) {
            return null;
        }

        return $previousCount < $currentCount ? 'success' : 'danger';
    }

    public function getBadgeIcon(): ?string
    {
        $previousCount = $this->previousCount;
        $currentCount = $this->currentCount;

        if ($previousCount == 0) {
            return null;
        }

        return $previousCount < $currentCount ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            7 => '7 Days',
            14 => '14 Days',
            30 => '30 Days',
            60 => '60 Days',
        ];
    }

    protected function getChartLabels(): array
    {
        return $this->generateDateRange();
    }

    private function generateDateRange(string $format = 'd M'): array
    {
        if ($this->filter === 'today') {
            return [now()->format($format)];
        }

        $labels = [];

        $startDate = now()->subDays($this->filter - 1);
        $endDate = now();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $labels[] = $date->format($format);
        }

        return $labels;
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                    'min' => 0,
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }

    protected function getChartData(): array
    {
        if ($query = $this->getQuery()) {
            $expression = "date_format({$this->getQuery()->getGrammar()->wrap($this->dateColumn)}, '%Y-%m-%d')";

            $dateRange = $this->generateDateRange('Y-m-d');

            $data = with($query)->whereBetween(
                $this->dateColumn,
                $this->currentRange($this->filter)
            )
                ->select(DB::raw("{$expression} as date_result, COUNT(*) as aggregate"))
                ->groupBy(DB::raw($expression))
                ->orderBy('date_result')
                ->get();

            return collect($dateRange)->reduce(function ($result, $date) use ($data) {
                $result[] = $data->where('date_result', $date)->first()?->aggregate ?? 0;

                return $result;
            }, []);
        }

        return [];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
