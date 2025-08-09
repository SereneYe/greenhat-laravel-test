<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\CourseCategory\UpdateCourseCategory;
use Modules\Course\Data\CourseCategory\UpdateCourseCategoryData;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryDuplicateException;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class UpdateCourseCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action updates a course category with valid data.
     */
    public function test_updates_course_category_with_valid_data(): void
    {
        // Create a category to update
        $category = CourseCategory::create([
            'name' => 'Original Name',
            'slug' => 'original-name',
            'description' => 'Original description',
            'color' => '#000000',
            'is_active' => true,
        ]);

        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category,
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'color' => '#FFFFFF',
            'is_active' => false,
        ]);

        $action = new UpdateCourseCategory();
        $updatedCategory = $action->handle($data);

        $this->assertInstanceOf(CourseCategory::class, $updatedCategory);
        $this->assertEquals('Updated Name', $updatedCategory->name);
        $this->assertEquals('updated-name', $updatedCategory->slug);
        $this->assertEquals('Updated description', $updatedCategory->description);
        $this->assertEquals('#FFFFFF', $updatedCategory->color);
        $this->assertFalse($updatedCategory->is_active);

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
     * Test that the action updates only the provided fields.
     */
    public function test_updates_only_provided_fields(): void
    {
        // Create a category to update
        $category = CourseCategory::create([
            'name' => 'Partial Update Test',
            'slug' => 'partial-update-test',
            'description' => 'Original description',
            'color' => '#000000',
            'is_active' => true,
        ]);

        // Only update the description
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category,
            'description' => 'Updated description only',
        ]);

        $action = new UpdateCourseCategory();
        $updatedCategory = $action->handle($data);

        // Name, slug, color, and is_active should remain unchanged
        $this->assertEquals('Partial Update Test', $updatedCategory->name);
        $this->assertEquals('partial-update-test', $updatedCategory->slug);
        $this->assertEquals('Updated description only', $updatedCategory->description);
        $this->assertEquals('#000000', $updatedCategory->color);
        $this->assertTrue($updatedCategory->is_active);
    }

    /**
     * Test that the action throws an exception for duplicate name.
     */
    public function test_throws_exception_for_duplicate_name(): void
    {
        // Create two categories
        $category1 = CourseCategory::create([
            'name' => 'First Category',
            'slug' => 'first-category',
        ]);

        $category2 = CourseCategory::create([
            'name' => 'Second Category',
            'slug' => 'second-category',
        ]);

        // Try to update the second category to have the same name as the first
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category2,
            'name' => 'First Category',
        ]);

        $action = new UpdateCourseCategory();

        $this->expectException(CourseCategoryDuplicateException::class);
        $action->handle($data);
    }

    /**
     * Test that the action generates a unique slug when there's a conflict.
     */
    public function test_generates_unique_slug_when_conflict(): void
    {
        // Create two categories
        $category1 = CourseCategory::create([
            'name' => 'First Category',
            'slug' => 'common-slug',
        ]);

        $category2 = CourseCategory::create([
            'name' => 'Second Category',
            'slug' => 'second-category',
        ]);

        // Update the second category to a name that would generate the same slug as the first
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category2,
            'name' => 'Common Slug',
        ]);

        $action = new UpdateCourseCategory();
        $updatedCategory = $action->handle($data);

        // The slug should be made unique by appending a number
        $this->assertNotEquals('common-slug', $updatedCategory->slug);
        $this->assertStringStartsWith('common-slug-', $updatedCategory->slug);
    }

    /**
     * Test that the action handles special characters in name correctly.
     */
    public function test_handles_special_characters_in_name(): void
    {
        $category = CourseCategory::create([
            'name' => 'Original Name',
            'slug' => 'original-name',
        ]);

        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category,
            'name' => 'Special & Characters: Test!',
        ]);

        $action = new UpdateCourseCategory();
        $updatedCategory = $action->handle($data);

        $this->assertEquals('Special & Characters: Test!', $updatedCategory->name);
        $this->assertEquals('special-characters-test', $updatedCategory->slug);
    }

    /**
     * Test that the action can toggle is_active status.
     */
    public function test_can_toggle_is_active_status(): void
    {
        // Create an active category
        $category = CourseCategory::create([
            'name' => 'Active Category',
            'slug' => 'active-category',
            'is_active' => true,
        ]);

        // Update to inactive
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category,
            'is_active' => false,
        ]);

        $action = new UpdateCourseCategory();
        $updatedCategory = $action->handle($data);

        $this->assertFalse($updatedCategory->is_active);

        // Update back to active
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $updatedCategory,
            'is_active' => true,
        ]);

        $updatedCategory = $action->handle($data);
        $this->assertTrue($updatedCategory->is_active);
    }

    /**
     * Test that the action doesn't update if no fields are provided.
     */
    public function test_no_update_if_no_fields_provided(): void
    {
        $category = CourseCategory::create([
            'name' => 'No Update Test',
            'slug' => 'no-update-test',
            'description' => 'Original description',
            'color' => '#000000',
            'is_active' => true,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        // Store the original updated_at timestamp
        $originalUpdatedAt = $category->updated_at;

        // Create data with no fields to update
        $data = UpdateCourseCategoryData::from([
            'courseCategory' => $category,
        ]);

        $action = new UpdateCourseCategory();
        $result = $action->handle($data);

        // The category should be returned unchanged
        $this->assertEquals($category->id, $result->id);
        $this->assertEquals('No Update Test', $result->name);

        // Refresh from database to check if updated_at changed
        $category->refresh();

        // The updated_at timestamp should not have changed
        $this->assertEquals($originalUpdatedAt->timestamp, $category->updated_at->timestamp);
    }
}
