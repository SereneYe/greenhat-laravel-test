<?php

namespace Modules\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

trait HasSlugAttribute
{
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function scopeByKeyNameOrSlug($query, $value)
    {
        return $query
            ->where(function ($query) use ($value) {
                $query->where($this->getKeyName(), 'like binary', $value)
                    ->orWhere('slug', 'like binary', $value);
            });
    }

    public static function findByKeyNameOrSlug($value): static
    {
        $model = (new static)
            ->findBySlug($value, ['*'], fn (Builder $query) => $query->orWhere((new static)->getKeyName(), 'like binary', $value));

        throw_if(empty($model), ModelNotFoundException::class);

        return $model;
    }
}
