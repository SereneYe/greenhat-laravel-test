<?php

namespace App\Filament\FilamentCustom\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;
use Filament\Support\RawJs;

class AdvancedDoughnutChartWidgetWithCount extends AdvancedChartWidget
{
    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 3;

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            filter: (legendItem, data) => data.datasets[0].data[legendItem.index] != 0,
                            generateLabels(chart) {
                                const data = chart.data
                                if (data.labels.length && data.datasets.length) {
                                    const {labels: {pointStyle}} = chart.legend.options
                
                                    return data.labels.map((label, i) => {
                                        const meta = chart.getDatasetMeta(0)
                                        const style = meta.controller.getStyle(i)
                                        const dataLabel = data['datasets'][0].data[i]
                                        const total = data['datasets'][0].data.reduce((partialSum, data) => partialSum + data, 0)
                                        return {
                                            text: label + ' (' + ((dataLabel/total) * 100).toFixed(2) + '%)',
                                            fillStyle: style.backgroundColor,
                                            strokeStyle: style.borderColor,
                                            lineWidth: style.borderWidth,
                                            pointStyle: pointStyle,
                                            hidden: !chart.getDataVisibility(i),
                
                                            // Extra data used for toggling the correct item
                                            index: i
                                        }
                                    })
                                }
                                return []
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            const datapoints = ctx.chart.data.datasets[0].data
                            const total = datapoints.reduce((total, datapoint) => total + datapoint, 0)
                            const percentage = value / total * 100
                            return percentage.toFixed(2)
                        },
                        color: '#fff',
                    }
                },
                scales: {
                  x: {
                    display: false
                  },
                  y: {
                    display: false
                  }
                },
                maintainAspectRatio: false,
                elements: {
                  arc: {
                    borderWidth: 0
                  }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
