<?php

namespace Modules\Base\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $timezone = 'UTC';

    public static function group(): string
    {
        return 'general';
    }
}
