<?php

namespace Modules\Base\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this
            ->newQuery()
            ->idOrUUID($value)
            ->firstOrFail();
    }

    public function scopeIdOrUUID($query, $value)
    {
        return $query
            ->where($this->getKeyName(), 'like binary', $value)
            ->orWhere('uuid', 'like binary', $value);
    }
}
