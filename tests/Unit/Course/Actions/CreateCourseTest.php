<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\Course\CreateCourse;
use Modules\Course\Data\Course\CreateCourseData;
use Modules\Course\Exceptions\Course\CourseDuplicateException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Media\Models\FilamentMediaLibrary;
use Tests\TestCase;

class CreateCourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action creates a course with valid data.
     */
    public function test_creates_course_with_valid_data(): void
    {
        $data = CreateCourseData::from([
            'title' => 'Test Course',
            'description' => 'This is a test course',
            'content' => 'Course content goes here',
            'instructor' => 'John Doe',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new CreateCourse();
        $course = $action->handle($data);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('Test Course', $course->title);
        $this->assertEquals('test-course', $course->slug);
        $this->assertEquals('This is a test course', $course->description);
        $this->assertEquals('Course content goes here', $course->content);
        $this->assertEquals('John Doe', $course->instructor);
        $this->assertEquals(10, $course->duration_hours);
        $this->assertEquals(99.99, $course->price);
        $this->assertEquals('beginner', $course->level);

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'This is a test course',
            'content' => 'Course content goes here',
            'instructor' => 'John Doe',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);
    }

    /**
     * Test that the action creates a course with categories.
     */
    public function test_creates_course_with_categories(): void
    {
        // Create categories first
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();

        $data = CreateCourseData::from([
            'title' => 'Course With Categories',
            'description' => 'This course has categories',
            'content' => 'Course content',
            'instructor' => 'Jane Smith',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
            'categories' => [$category1->id, $category2->id],
        ]);

        $action = new CreateCourse();
        $course = $action->handle($data);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('Course With Categories', $course->title);

        // Check that the categories were attached
        $this->assertEquals(2, $course->categories()->count());
        $this->assertTrue($course->categories->contains($category1->id));
        $this->assertTrue($course->categories->contains($category2->id));
    }

    /**
     * Test that the action generates a unique slug from the title.
     */
    public function test_generates_unique_slug_from_title(): void
    {
        $data = CreateCourseData::from([
            'title' => 'Special & Characters: Test!',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new CreateCourse();
        $course = $action->handle($data);

        $this->assertEquals('special-characters-test', $course->slug);
    }

    /**
     * Test that the action throws an exception for duplicate title.
     */
    public function test_throws_exception_for_duplicate_title(): void
    {
        // Create a course first
        Course::create([
            'title' => 'Existing Course',
            'slug' => 'existing-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $data = CreateCourseData::from([
            'title' => 'Existing Course',
            'description' => 'This should fail',
            'duration_hours' => 5,
            'price' => 49.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new CreateCourse();

        $this->expectException(CourseDuplicateException::class);
        $action->handle($data);
    }

    /**
     * Test that the action throws an exception for invalid categories.
     */
    public function test_throws_exception_for_invalid_categories(): void
    {
        $data = CreateCourseData::from([
            'title' => 'Invalid Categories Course',
            'description' => 'This should fail',
            'duration_hours' => 5,
            'price' => 49.99,
            'level' => 'beginner',
            'categories' => [999, 1000], // Non-existent category IDs
        ]);

        $action = new CreateCourse();

        $this->expectException(\Exception::class);
        $action->handle($data);
    }


    /**
     * Test that the action attaches categories after creation.
     */
    public function test_attaches_categories_after_creation(): void
    {
        // Create categories
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();

        $data = CreateCourseData::from([
            'title' => 'Categories Attachment Test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [$category1->id, $category2->id],
        ]);

        $action = new CreateCourse();
        $course = $action->handle($data);

        // Check that the categories were attached
        $this->assertEquals(2, $course->categories()->count());
        $this->assertTrue($course->categories->contains($category1->id));
        $this->assertTrue($course->categories->contains($category2->id));

        // Verify in the database
        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category2->id,
        ]);
    }

    /**
     * Test that the action handles empty categories array.
     */
    public function test_handles_empty_categories_array(): void
    {
        $data = CreateCourseData::from([
            'title' => 'No Categories Course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $action = new CreateCourse();
        $course = $action->handle($data);

        // Check that no categories were attached
        $this->assertEquals(0, $course->categories()->count());
    }
}
