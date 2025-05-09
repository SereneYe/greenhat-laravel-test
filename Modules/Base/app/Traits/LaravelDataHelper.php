<?php

namespace Modules\Base\Traits;

use Carbon\Carbon;
use Exception;
use Spatie\LaravelData\Optional;

trait LaravelDataHelper
{
    public function has(string $key): bool
    {
        return ! $this->$key instanceof Optional;
    }

    public function convertToUtc(string $field): ?Carbon
    {
        if (! $this->has($field)) {
            return null;
        }

        if (! $value = $this->$field) {
            return null;
        }

        try {
            return Carbon::parse($value)->setTimezone('UTC');
        } catch (Exception $exception) {
            return null;
        }
    }
}
