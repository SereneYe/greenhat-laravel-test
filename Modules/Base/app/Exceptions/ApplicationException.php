<?php

namespace Modules\Base\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;

class ApplicationException extends Exception
{
    protected array $context = [];

    public function render(): JsonResponse
    {
        return response()->json([
            ...$this->context,
            'message' => $this->message,
        ], $this->code);
    }

    public function addContext(array $context): self
    {
        Context::add(class_basename($this), $context);

        $this->context = $context;

        return $this;
    }
}
