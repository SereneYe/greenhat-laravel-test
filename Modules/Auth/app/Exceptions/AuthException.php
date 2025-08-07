<?php

namespace Modules\Auth\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Throwable;

class AuthException extends Exception
{
    protected int $statusCode;
    protected array $context;

    public function __construct(string $message, int $statusCode = 422, array $context = [], ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);

        $this->statusCode = $statusCode;
        $this->context = $context;
    }

    public function render(): JsonResponse
    {
        return response()->json(array_merge(
            $this->context,
            ['message' => $this->message]
        ), $this->statusCode);
    }

    // Static factory methods for auth-related errors
    public static function invalidVerificationCode(): static
    {
        return new static('Invalid or expired verification code', 422);
    }

    public static function verificationCodeRequired(): static
    {
        return new static('Verification code is required', 422);
    }

    public static function verificationCodeExpired(): static
    {
        return new static('Verification code has expired', 422);
    }

    public static function invalidCredentials(): static
    {
        return new static('The provided credentials are incorrect', 401);
    }

    public static function userRegistrationFailed(string $reason = ''): static
    {
        $message = 'User registration failed';
        if ($reason) {
            $message .= ': ' . $reason;
        }
        return new static($message, 422);
    }

    public static function tooManyLoginAttempts(int $seconds): static
    {
        return new static(
            "Too many login attempts. Please try again in {$seconds} seconds.",
            429,
            ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]
        );
    }

    // Password reset related exceptions
    public static function userNotFound(): static
    {
        return new static('No account found with this email address', 404);
    }

    public static function passwordResetFailed(string $reason = ''): static
    {
        $message = 'Password reset failed';
        if ($reason) {
            $message .= ': ' . $reason;
        }
        return new static($message, 422);
    }
}
