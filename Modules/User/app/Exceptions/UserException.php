<?php

namespace Modules\User\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserException extends Exception
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

    // usr authentication failed
    public static function authenticationFailed(): static
    {
        return new static('Authentication failed', 401);
    }

    public static function userNotFound(): static
    {
        return new static('User not found', 404);
    }

    // registration failed
    public static function registrationFailed(string $reason = ''): static
    {
        $message = 'User registration failed';
        if ($reason) {
            $message .= ': ' . $reason;
        }
        return new static($message, 422);
    }

    // verification code related exceptions
    public static function verificationCodeRequired(): static
    {
        return new static('Verification code is required', 422);
    }

    public static function invalidVerificationCode(): static
    {
        return new static('Invalid or expired verification code', 422);
    }

    public static function verificationCodeExpired(): static
    {
        return new static('Verification code has expired', 422);
    }

    // reset password related exceptions
    public static function passwordResetFailed(string $reason = ''): static
    {
        $message = 'Password reset failed';
        if ($reason) {
            $message .= ': ' . $reason;
        }
        return new static($message, 422);
    }
}
