<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable authentication middleware for testing
        $this->withoutMiddleware([
            \Illuminate\Auth\Middleware\Authenticate::class,
        ]);
    }
}
