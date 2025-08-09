<?php

namespace Tests\Unit\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseCategoryFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the CourseCategory factory works correctly.
     */
    public function test_course_category_factory_creates_valid_instance(): void
    {
        // Create a category using the factory
        $category = CourseCategory::factory()->create();

        // Assert that the category was created and has the expected attributes
        $this->assertNotNull($category);
        $this->assertIsString($category->name);
        $this->assertIsString($category->slug);
        $this->assertIsString($category->color);
        $this->assertIsBool($category->is_active);

        // Verify it exists in the database
        $this->assertDatabaseHas('course_categories', [
            'id' => $category->id,
        ]);
    }
}
