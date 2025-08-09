<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\Course\UpdateCourse;
use Modules\Course\Data\Course\UpdateCourseData;
use Modules\Course\Exceptions\Course\CourseDuplicateException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Media\Models\FilamentMediaLibrary;
use Tests\TestCase;

class UpdateCourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action updates course basic fields.
     */
    public function test_updates_course_basic_fields(): void
    {
        // Create a course to update
        $course = Course::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'description' => 'Original description',
            'content' => 'Original content',
            'instructor' => 'Original Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $data = UpdateCourseData::from([
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'content' => 'Updated content',
            'instructor' => 'Updated Instructor',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
            'categories' => [],
        ]);

        $action = new UpdateCourse();
        $updatedCourse = $action->handle($course->id, $data);

        $this->assertEquals('Updated Title', $updatedCourse->title);
        $this->assertEquals('updated-title', $updatedCourse->slug);
        $this->assertEquals('Updated description', $updatedCourse->description);
        $this->assertEquals('Updated content', $updatedCourse->content);
        $this->assertEquals('Updated Instructor', $updatedCourse->instructor);
        $this->assertEquals(15, $updatedCourse->duration_hours);
        $this->assertEquals(149.99, $updatedCourse->price);
        $this->assertEquals('intermediate', $updatedCourse->level);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'description' => 'Updated description',
            'content' => 'Updated content',
            'instructor' => 'Updated Instructor',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
        ]);
    }

    /**
     * Test that the action updates course categories.
     */
    public function test_updates_course_categories(): void
    {
        // Create a course and categories
        $course = Course::create([
            'title' => 'Course for Category Update',
            'slug' => 'course-for-category-update',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $category3 = CourseCategory::factory()->create();

        // Initially attach category1
        $course->categories()->attach($category1->id);

        // Update to use category2 and category3 instead
        $data = UpdateCourseData::from([
            'title' => 'Course for Category Update',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [$category2->id, $category3->id],
        ]);

        $action = new UpdateCourse();
        $updatedCourse = $action->handle($course->id, $data);

        // Check that the categories were updated
        $this->assertEquals(2, $updatedCourse->categories()->count());
        $this->assertFalse($updatedCourse->categories->contains($category1->id));
        $this->assertTrue($updatedCourse->categories->contains($category2->id));
        $this->assertTrue($updatedCourse->categories->contains($category3->id));
    }

    /**
     * Test that the action syncs categories correctly.
     */
    public function test_syncs_categories_correctly(): void
    {
        // Create a course and categories
        $course = Course::create([
            'title' => 'Category Sync Test',
            'slug' => 'category-sync-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $category3 = CourseCategory::factory()->create();

        // Initially attach category1 and category2
        $course->categories()->attach([$category1->id, $category2->id]);

        // Update to use category2 and category3 instead
        $data = UpdateCourseData::from([
            'title' => 'Category Sync Test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [$category2->id, $category3->id],
        ]);

        $action = new UpdateCourse();
        $updatedCourse = $action->handle($course->id, $data);

        // Check that category1 was detached, category2 remained, and category3 was attached
        $this->assertEquals(2, $updatedCourse->categories()->count());
        $this->assertFalse($updatedCourse->categories->contains($category1->id));
        $this->assertTrue($updatedCourse->categories->contains($category2->id));
        $this->assertTrue($updatedCourse->categories->contains($category3->id));

        // Verify in the database
        $this->assertDatabaseMissing('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category2->id,
        ]);

        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category3->id,
        ]);
    }

    /**
     * Test that the action updates slug when title changes.
     */
    public function test_updates_slug_when_title_changes(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Update the title
        $data = UpdateCourseData::from([
            'title' => 'New Title',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new UpdateCourse();
        $updatedCourse = $action->handle($course->id, $data);

        // Check that the slug was updated to match the new title
        $this->assertEquals('new-title', $updatedCourse->slug);
    }

    /**
     * Test that the action throws an exception for invalid categories.
     */
    public function test_throws_exception_for_invalid_categories(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Invalid Categories Test',
            'slug' => 'invalid-categories-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Update with invalid categories
        $data = UpdateCourseData::from([
            'title' => 'Invalid Categories Test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [999, 1000], // Non-existent category IDs
        ]);

        $action = new UpdateCourse();

        $this->expectException(\Exception::class);
        $action->handle($course->id, $data);
    }


    /**
     * Test that the action removes all categories when empty array provided.
     */
    public function test_removes_all_categories_when_empty_array_provided(): void
    {
        // Create a course and categories
        $course = Course::create([
            'title' => 'Remove Categories Test',
            'slug' => 'remove-categories-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();

        // Initially attach categories
        $course->categories()->attach([$category1->id, $category2->id]);

        // Update with empty categories array
        $data = UpdateCourseData::from([
            'title' => 'Remove Categories Test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new UpdateCourse();
        $updatedCourse = $action->handle($course->id, $data);

        // Check that all categories were removed
        $this->assertEquals(0, $updatedCourse->categories()->count());

        // Verify in the database
        $this->assertDatabaseMissing('course_course_category', [
            'course_id' => $course->id,
        ]);
    }
}
