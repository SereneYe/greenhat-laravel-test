<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\CourseCategory\GetCourseCategory;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryNotFoundException;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class GetCourseCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action retrieves a course category by ID.
     */
    public function test_retrieves_course_category_by_id(): void
    {
        // Create a category
        $category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#123456',
            'is_active' => true,
        ]);

        $action = new GetCourseCategory();
        $retrievedCategory = $action->handle($category->id);

        $this->assertInstanceOf(CourseCategory::class, $retrievedCategory);
        $this->assertEquals($category->id, $retrievedCategory->id);
        $this->assertEquals('Test Category', $retrievedCategory->name);
        $this->assertEquals('test-category', $retrievedCategory->slug);
        $this->assertEquals('Test description', $retrievedCategory->description);
        $this->assertEquals('#123456', $retrievedCategory->color);
        $this->assertTrue($retrievedCategory->is_active);
    }

    /**
     * Test that the action retrieves a course category by slug.
     */
    public function test_retrieves_course_category_by_slug(): void
    {
        // Create a category
        $category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
            'color' => '#123456',
            'is_active' => true,
        ]);

        $action = new GetCourseCategory();
        $retrievedCategory = $action->handle('test-category', true);

        $this->assertInstanceOf(CourseCategory::class, $retrievedCategory);
        $this->assertEquals($category->id, $retrievedCategory->id);
        $this->assertEquals('Test Category', $retrievedCategory->name);
        $this->assertEquals('test-category', $retrievedCategory->slug);
    }

    /**
     * Test that the action throws an exception for non-existent ID.
     */
    public function test_throws_exception_for_non_existent_id(): void
    {
        $action = new GetCourseCategory();

        $this->expectException(CourseCategoryNotFoundException::class);
        $action->handle(999); // Non-existent ID
    }

    /**
     * Test that the action throws an exception for non-existent slug.
     */
    public function test_throws_exception_for_non_existent_slug(): void
    {
        $action = new GetCourseCategory();

        $this->expectException(CourseCategoryNotFoundException::class);
        $action->handle('non-existent-slug', true);
    }

    /**
     * Test that the action only retrieves active categories when specified.
     */
    public function test_only_retrieves_active_categories_when_specified(): void
    {
        // Create an inactive category
        $category = CourseCategory::create([
            'name' => 'Inactive Category',
            'slug' => 'inactive-category',
            'is_active' => false,
        ]);

        // The standard handle method should retrieve the category regardless of active status
        $action = new GetCourseCategory();
        $retrievedCategory = $action->handle($category->id);

        $this->assertInstanceOf(CourseCategory::class, $retrievedCategory);
        $this->assertEquals($category->id, $retrievedCategory->id);
        $this->assertFalse($retrievedCategory->is_active);
    }

    /**
     * Test that the action doesn't retrieve soft deleted categories.
     */
    public function test_does_not_retrieve_soft_deleted_categories(): void
    {
        // Create a category
        $category = CourseCategory::create([
            'name' => 'Deleted Category',
            'slug' => 'deleted-category',
        ]);

        // Soft delete the category
        $category->delete();

        $action = new GetCourseCategory();

        // Should throw an exception when trying to retrieve by ID
        $this->expectException(CourseCategoryNotFoundException::class);
        $action->handle($category->id);
    }

    /**
     * Test that the action handles special characters in slug correctly.
     */
    public function test_handles_special_characters_in_slug(): void
    {
        // Create a category with a slug containing special characters
        $category = CourseCategory::create([
            'name' => 'Special Characters',
            'slug' => 'special-characters-test',
        ]);

        $action = new GetCourseCategory();
        $retrievedCategory = $action->handle('special-characters-test', true);

        $this->assertInstanceOf(CourseCategory::class, $retrievedCategory);
        $this->assertEquals($category->id, $retrievedCategory->id);
        $this->assertEquals('special-characters-test', $retrievedCategory->slug);
    }
}
