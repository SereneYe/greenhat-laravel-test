<?php

namespace Modules\User\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\IP;
use Spatie\LaravelData\Data;

class AuthenticateUserData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        #[IP]
        public string $ipAddress
    ) {
    }
}
