<?php

namespace Modules\Auth\Data;

use Modules\Auth\Http\Requests\LoginRequest;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {}

    /**
     * Create a new DTO instance from a request.
     */
    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
            remember: $request->boolean('remember'),
        );
    }

    /**
     * Get credentials for authentication.
     */
    public function getCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    /**
     * Get remember flag.
     */
    public function shouldRemember(): bool
    {
        return $this->remember;
    }
}
