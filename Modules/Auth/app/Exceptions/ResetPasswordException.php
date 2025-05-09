<?php

namespace Modules\Auth\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Throwable;

class ResetPasswordException extends Exception
{
    /** @var string $message */
    protected $message;

    public function __construct(string $message = "", $code = 0, Throwable $previous = null)
    {
        $this->message = $message;

        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => $this->message
        ], 422);
    }

    public static function invalidToken(): static
    {
        return new static(Str::snake(__FUNCTION__));
    }
}
