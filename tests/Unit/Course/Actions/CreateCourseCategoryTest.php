<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\CourseCategory\CreateCourseCategory;
use Modules\Course\Data\CourseCategory\CreateCourseCategoryData;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryDuplicateException;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CreateCourseCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action creates a course category with valid data.
     */
    public function test_creates_course_category_with_valid_data(): void
    {
        $data = CreateCourseCategoryData::from([
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'color' => '#FF5733',
            'is_active' => true,
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        $this->assertInstanceOf(CourseCategory::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('This is a test category', $category->description);
        $this->assertEquals('#FF5733', $category->color);
        $this->assertTrue($category->is_active);

        $this->assertDatabaseHas('course_categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category',
            'color' => '#FF5733',
            'is_active' => true,
        ]);
    }

    /**
     * Test that the action creates a course category with minimal data.
     */
    public function test_creates_course_category_with_minimal_data(): void
    {
        $data = CreateCourseCategoryData::from([
            'name' => 'Minimal Category',
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        $this->assertInstanceOf(CourseCategory::class, $category);
        $this->assertEquals('Minimal Category', $category->name);
        $this->assertEquals('minimal-category', $category->slug);
        $this->assertNull($category->description);
        $this->assertNull($category->color);
        $this->assertTrue($category->is_active); // Default value

        $this->assertDatabaseHas('course_categories', [
            'name' => 'Minimal Category',
            'slug' => 'minimal-category',
            'is_active' => true,
        ]);
    }

    /**
     * Test that the action throws an exception for duplicate name.
     */
    public function test_throws_exception_for_duplicate_name(): void
    {
        // Create a category first
        CourseCategory::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category',
            'color' => '#000000',
            'is_active' => true,
        ]);

        $data = CreateCourseCategoryData::from([
            'name' => 'Existing Category',
            'description' => 'This should fail',
        ]);

        $action = new CreateCourseCategory();

        $this->expectException(CourseCategoryDuplicateException::class);
        $action->handle($data);
    }

    /**
     * Test that the action generates a unique slug when there's a conflict.
     */
    public function test_generates_unique_slug_when_conflict(): void
    {
        // Create a category first with a specific slug
        CourseCategory::create([
            'name' => 'Original Category',
            'slug' => 'test-slug',
            'color' => '#000000',
            'is_active' => true,
        ]);

        // Create a new category that would normally generate the same slug
        $data = CreateCourseCategoryData::from([
            'name' => 'Test Slug',
            'description' => 'This should get a unique slug',
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        // The slug should be made unique by appending a number
        $this->assertNotEquals('test-slug', $category->slug);
        $this->assertStringStartsWith('test-slug-', $category->slug);
    }

    /**
     * Test that the action handles special characters in name correctly.
     */
    public function test_handles_special_characters_in_name(): void
    {
        $data = CreateCourseCategoryData::from([
            'name' => 'Special & Characters: Test!',
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        $this->assertEquals('Special & Characters: Test!', $category->name);
        $this->assertEquals('special-characters-test', $category->slug);
    }

    /**
     * Test that the action sets is_active to true by default.
     */
    public function test_sets_is_active_to_true_by_default(): void
    {
        $data = CreateCourseCategoryData::from([
            'name' => 'Default Active Test',
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        $this->assertTrue($category->is_active);
    }

    /**
     * Test that the action can create an inactive category.
     */
    public function test_can_create_inactive_category(): void
    {
        $data = CreateCourseCategoryData::from([
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);

        $action = new CreateCourseCategory();
        $category = $action->handle($data);

        $this->assertFalse($category->is_active);
    }
}
