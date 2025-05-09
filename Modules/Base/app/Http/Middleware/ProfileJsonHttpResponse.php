<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileJsonHttpResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (
            $response instanceof JsonResponse &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled() &&
            $request->hasHeader('X-DEBUGBAR')
        ) {
            $queries = $this->sqlFilter(app('debugbar')->getData());

            $content = [
                'data' => $response->getData(),
                '_debugbar' => [
                    'total_queries' => count($queries),
                    'queries' => $queries,
                    'data' => collect(app('debugbar')->getData())->filter(function($value, $key) {
                        return in_array($key, ['time', 'memory']);
                    })->toArray()
                ]
            ];

            $response->setContent(json_encode($content, true));
        }

        return $response;
    }

    /**
     * Get only sql and each duration
     *
     * @param $debugbar_data
     * @return array
     */
    protected function sqlFilter($debugbar_data) {
        $result = Arr::get($debugbar_data, 'queries.statements');

        return array_map(function ($item) {
            return [
                'sql' => Arr::get($item, 'sql'),
                'duration' => Arr::get($item, 'duration_str'),
            ];
        }, $result);
    }
}
