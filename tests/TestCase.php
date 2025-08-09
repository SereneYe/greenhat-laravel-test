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

    /**
     * Create a test media record for use in tests.
     * This is a workaround for the missing FilamentMediaLibrary factory.
     * Creates a mock object and inserts a record directly into the database.
     *
     * @param array $attributes Override default attributes
     * @return object A mock FilamentMediaLibrary object
     */
    protected function createTestMedia(array $attributes = []): object
    {
        $defaultAttributes = [
            'id' => rand(1, 1000),
            'name' => 'test-image',
            'file_name' => 'test-image.jpg',
            'mime_type' => 'image/jpeg',
            'path' => 'test/test-image.jpg',
            'disk' => 'public',
            'size' => 1024,
            'alt_text' => 'Test image',
            'title' => 'Test Image',
            'caption' => 'Test caption',
            'uploaded_by_user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $mediaAttributes = array_merge($defaultAttributes, $attributes);

        // Insert a record directly into the database using a raw SQL query
        \Illuminate\Support\Facades\DB::table('filament_media_library')->insert([
            'id' => $mediaAttributes['id'],
            'caption' => $mediaAttributes['caption'],
            'alt_text' => $mediaAttributes['alt_text'],
            'uploaded_by_user_id' => $mediaAttributes['uploaded_by_user_id'],
            'created_at' => $mediaAttributes['created_at'],
            'updated_at' => $mediaAttributes['updated_at'],
        ]);

        // Create a mock object that can be used in place of FilamentMediaLibrary
        return new class($mediaAttributes) {
            protected $attributes;

            public function __construct($attributes)
            {
                $this->attributes = $attributes;
            }

            public function __get($name)
            {
                return $this->attributes[$name] ?? null;
            }
        };
    }

    /**
     * Create a test employee record for use in tests.
     * This is a workaround for issues with the Employee factory.
     *
     * @param array $attributes Override default attributes
     * @return \Modules\Employee\Models\Employee
     */
    protected function createTestEmployee(array $attributes = []): \Modules\Employee\Models\Employee
    {
        // Create a user first
        $user = \Modules\User\Models\User::create([
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'name' => 'Test Employee ' . uniqid(),
            'email' => 'employee_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        $defaultAttributes = [
            'user_id' => $user->id,
            'role' => 'Family Support Leader',
        ];

        return \Modules\Employee\Models\Employee::create(array_merge($defaultAttributes, $attributes));
    }
}
