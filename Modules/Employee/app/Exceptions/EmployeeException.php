<?php

namespace Modules\Employee\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PHPUnit\Event\Code\Throwable;

class EmployeeException extends Exception
{
    protected int $statusCode;

    protected array $info;

    public function __construct(string $message, int $statusCode = 422, array $info = [], ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);

        $this->statusCode = $statusCode;

        $this->info = $info;
    }

    public function render(): JsonResponse
    {
        return response()->json(array_merge(
            $this->info,
            [
                'message' => $this->message,
            ]
        ), $this->statusCode);
    }

    public static function isNotEmployee(): static
    {
        return new static(Str::snake(__FUNCTION__));
    }

    public static function isNotActive(array $info = [], int $statusCode = 403): static
    {
        return new static(Str::snake(__FUNCTION__), $statusCode, $info);
    }
}
