<?php

namespace Modules\Course\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Base\Traits\ModuleRoutes;

class RouteServiceProvider extends ServiceProvider
{
    use ModuleRoutes;

    /**
     * Define the routes for the application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiRoutes(): void
    {
        $this->loadApiRoutes();
    }

    /**
     * Load API routes.
     */
    protected function loadApiRoutes(): void
    {
        if (file_exists($this->getRouteDirectory() . '/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group($this->getRouteDirectory() . '/api.php');
        }
    }

    protected function getRouteDirectory(): string
    {
        return module_path('Course', 'routes');
    }
}
