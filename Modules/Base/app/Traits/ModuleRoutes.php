<?php

namespace Modules\Base\Traits;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Modules\Base\Http\Middleware\JsonResponseMiddleware;
use Modules\Base\Http\Middleware\ProfileJsonHttpResponse;
use Symfony\Component\Finder\SplFileInfo;

trait ModuleRoutes
{
    abstract protected function getRouteDirectory(): string;

    public function map(): void
    {
        collect(File::files($this->getRouteDirectory()))->each(function (SplFileInfo $file) {
            if ($file->getFilename() === 'web.php') {
                Route::middleware('web')->group($file);

                return;
            }

            try {
                [$name, $version, $visibility] = explode('.', $file->getFilename());

            } catch (Exception $exception) {
                return;
            }

            if (in_array($visibility, ['deprecated', 'draft'])) {
                return;
            }

            $route = Route::prefix($version)->name($name.'_'.$version);

            $middlewares = match ($visibility) {
                'html', 'webhook' => [],
                'private' => [
                    'auth:sanctum',
                ],
                'public' => [
                    JsonResponseMiddleware::class,
                ],
                'signed' => [
                    'signed',
                ],
                default => throw new InvalidArgumentException
            };

            $route->middleware([
                ...$middlewares,
                ProfileJsonHttpResponse::class
            ]);

            $route->group($file);
        });
    }
}
