<?php

namespace Modules\User\Data\Auth;

use Spatie\LaravelData\Data;

class RegisterUserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public ?string $verificationCode = null,
    ) {}

    public static function fromAuthRequest(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            password: $data['password'],
            verificationCode: $data['verification_code'] ?? null,
        );
    }
}
