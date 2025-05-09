<?php

namespace Modules\Base\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SanitizedHtmlCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): mixed
    {
        return str($value)->sanitizeHtml();
    }

    public function set($model, $key, $value, $attributes): mixed
    {
        return str($value)->sanitizeHtml();
    }
}
