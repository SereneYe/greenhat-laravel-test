<?php

namespace App\Filament\FilamentCustom\Tables\Columns\Traits;

use Filament\Tables\Columns\Concerns\HasColor;

trait ColumnColor
{
    public function success(): static
    {
        $this->color('success');

        return $this;
    }

    public function warning(): static
    {
        $this->color('warning');

        return $this;
    }

    public function gray(): static
    {
        $this->color('gray');

        return $this;
    }

    public function danger(): static
    {
        $this->color('danger');

        return $this;
    }
}
