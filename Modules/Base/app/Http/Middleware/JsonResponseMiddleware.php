<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JsonResponseMiddleware
{
    public function __construct(
        protected ResponseFactory $responseFactory
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        // First, set the header so any other middleware knows we're
        // dealing with a should-be JSON response.
        $request->headers->set('Accept', 'application/json');

        // Get the response
        $response = $next($request);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        // If the response is not strictly a JsonResponse, we make it
        if (! $response instanceof JsonResponse) {
            $response = $this->responseFactory->json(
                json_decode($response->content()),
                $response->status(),
                $response->headers->all()
            );
        }

        return $response;
    }
}
