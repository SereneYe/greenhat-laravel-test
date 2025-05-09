<?php

namespace Modules\User\Data\User;

use Modules\User\Models\User;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class CreateUserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        #[Unique(User::class, 'email'), Email()]
        public string $email,
        public string $password
    ) {}
}
