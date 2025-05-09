<?php

namespace Modules\Auth\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Base\Traits\ModuleRoutes;

class RouteServiceProvider extends ServiceProvider
{
    use ModuleRoutes;

    protected function getRouteDirectory(): string
    {
        return __DIR__.'/../../routes';
    }
}
