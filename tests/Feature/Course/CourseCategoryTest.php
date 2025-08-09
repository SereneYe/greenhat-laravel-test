<?php

namespace Tests\Feature\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a course category with valid data.
     */
    public function test_can_create_course_category_with_valid_data(): void
    {
        $data = [
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'color' => '#FF5733',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/course-categories', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'color',
                    'isActive',
                    'coursesCount',
                    'activeCoursesCount',
                    'createdAt',
                    'updatedAt',
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Test Category',
                    'slug' => 'test-category',
                    'description' => 'This is a test category',
                    'color' => '#FF5733',
                    'isActive' => true,
                    'coursesCount' => 0,
                    'activeCoursesCount' => 0,
                ]
            ]);

        $this->assertDatabaseHas('course_categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category',
            'color' => '#FF5733',
            'is_active' => true,
        ]);
    }

    /**
     * Test that a category cannot be created with a duplicate name.
     */
    public function test_cannot_create_course_category_with_duplicate_name(): void
    {
        // Create a category first
        CourseCategory::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category',
            'is_active' => true,
        ]);

        // Try to create another with the same name
        $data = [
            'name' => 'Existing Category',
            'description' => 'This should fail',
        ];

        $response = $this->postJson('/api/v1/course-categories', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test updating a course category.
     */
    public function test_can_update_course_category(): void
    {
        $category = CourseCategory::create([
            'name' => 'Original Name',
            'slug' => 'original-name',
            'description' => 'Original description',
            'color' => '#000000',
            'is_active' => true,
        ]);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'color' => '#FFFFFF',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/v1/course-categories/{$category->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Name',
                    'slug' => 'updated-name',
                    'description' => 'Updated description',
                    'color' => '#FFFFFF',
                    'isActive' => false,
                ]
            ]);

        $this->assertDatabaseHas('course_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'description' => 'Updated description',
            'color' => '#FFFFFF',
            'is_active' => false,
        ]);
    }

    /**
     * Test deleting a course category.
     */
    public function test_can_delete_course_category(): void
    {
        $category = CourseCategory::create([
            'name' => 'Category to Delete',
            'slug' => 'category-to-delete',
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/v1/course-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Course category deleted successfully',
            ]);

        // Since we're using soft deletes, the record should still exist but with a deleted_at timestamp
        $this->assertSoftDeleted('course_categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * Test getting a course category by ID.
     */
    public function test_can_get_course_category_by_id(): void
    {
        $category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#123456',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/course-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Test Category',
                    'slug' => 'test-category',
                    'description' => 'Test description',
                    'color' => '#123456',
                    'isActive' => true,
                ]
            ]);
    }

    /**
     * Test getting a course category by slug.
     */
    public function test_can_get_course_category_by_slug(): void
    {
        $category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#123456',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/course-categories/slug/test-category");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => 'Test Category',
                    'slug' => 'test-category',
                ]
            ]);
    }


    /**
     * Test that a 404 is returned for a non-existent category.
     */
    public function test_returns_404_for_non_existent_category(): void
    {
        $response = $this->getJson('/api/v1/course-categories/999');
        $response->assertStatus(404);
    }

    /**
     * Test that name is required when creating a category.
     */
    public function test_name_is_required(): void
    {
        $response = $this->postJson('/api/v1/course-categories', [
            'description' => 'Missing name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that name must be unique.
     */
    public function test_name_must_be_unique(): void
    {
        CourseCategory::create([
            'name' => 'Unique Name',
            'slug' => 'unique-name',
        ]);

        $response = $this->postJson('/api/v1/course-categories', [
            'name' => 'Unique Name',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that color must be a valid hex code.
     */
    public function test_color_must_be_valid_hex_code(): void
    {
        $response = $this->postJson('/api/v1/course-categories', [
            'name' => 'Invalid Color Category',
            'color' => 'not-a-hex-color',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['color']);
    }

    /**
     * Test that description has a maximum length.
     */
    public function test_description_has_maximum_length(): void
    {
        $response = $this->postJson('/api/v1/course-categories', [
            'name' => 'Long Description Category',
            'description' => Str::repeat('a', 1001), // 1001 characters
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    /**
     * Test that the API response structure matches the Fractal format.
     */
    public function test_api_response_structure_matches_fractal_format(): void
    {
        $category = CourseCategory::create([
            'name' => 'Fractal Test',
            'slug' => 'fractal-test',
            'description' => 'Testing Fractal response format',
            'color' => '#ABCDEF',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/course-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'color',
                    'isActive',
                    'coursesCount',
                    'activeCoursesCount',
                    'createdAt',
                    'updatedAt',
                ]
            ]);
    }

}
