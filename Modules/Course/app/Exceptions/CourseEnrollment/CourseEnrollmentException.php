<?php

namespace Modules\Course\Exceptions\CourseEnrollment;

use Exception;
use Illuminate\Http\JsonResponse;
use PHPUnit\Event\Code\Throwable;

class CourseEnrollmentException extends Exception
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

    public static function notFound(int $id): static
    {
        return new static("Course enrollment with ID {$id} not found.", 404);
    }

    public static function validationFailed(array $errors): static
    {
        return new static(
            'The given data was invalid.',
            422,
            ['errors' => $errors]
        );
    }
}
