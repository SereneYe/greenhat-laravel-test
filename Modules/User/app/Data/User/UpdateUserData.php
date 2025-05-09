<?php

namespace Modules\User\Data\User;

use Modules\Base\Traits\LaravelDataHelper;
use Modules\User\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateUserData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        public User $user,
        public string|Optional $firstName,
        public string|Optional $lastName,
        public string|Optional $email,
        public string|Optional $password
    ) {}
}
