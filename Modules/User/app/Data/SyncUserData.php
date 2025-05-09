<?php

namespace Modules\User\Data;

use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Data;

class SyncUserData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
