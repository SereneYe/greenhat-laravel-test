<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\Course\GetCourse;
use Modules\Course\Exceptions\Course\CourseNotFoundException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Media\Models\FilamentMediaLibrary;
use Tests\TestCase;

class GetCourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action retrieves a course by ID.
     */
    public function test_retrieves_course_by_id(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test description',
            'content' => 'Test content',
            'instructor' => 'Test Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $action = new GetCourse();
        $retrievedCourse = $action->handle($course->id);

        $this->assertInstanceOf(Course::class, $retrievedCourse);
        $this->assertEquals($course->id, $retrievedCourse->id);
        $this->assertEquals('Test Course', $retrievedCourse->title);
        $this->assertEquals('test-course', $retrievedCourse->slug);
        $this->assertEquals('Test description', $retrievedCourse->description);
        $this->assertEquals('Test content', $retrievedCourse->content);
        $this->assertEquals('Test Instructor', $retrievedCourse->instructor);
        $this->assertEquals(10, $retrievedCourse->duration_hours);
        $this->assertEquals(99.99, $retrievedCourse->price);
        $this->assertEquals('beginner', $retrievedCourse->level);
    }

    /**
     * Test that the action retrieves a course by slug.
     */
    public function test_retrieves_course_by_slug(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Slug Test Course',
            'slug' => 'slug-test-course',
            'description' => 'Test description',
            'content' => 'Test content',
            'instructor' => 'Test Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $action = new GetCourse();
        $retrievedCourse = $action->handleBySlug('slug-test-course');

        $this->assertInstanceOf(Course::class, $retrievedCourse);
        $this->assertEquals($course->id, $retrievedCourse->id);
        $this->assertEquals('Slug Test Course', $retrievedCourse->title);
        $this->assertEquals('slug-test-course', $retrievedCourse->slug);
    }

    /**
     * Test that the action throws an exception for non-existent course ID.
     */
    public function test_throws_exception_for_non_existent_course_id(): void
    {
        $action = new GetCourse();

        $this->expectException(CourseNotFoundException::class);
        $action->handle(999); // Non-existent ID
    }

    /**
     * Test that the action throws an exception for non-existent course slug.
     */
    public function test_throws_exception_for_non_existent_course_slug(): void
    {
        $action = new GetCourse();

        $this->expectException(CourseNotFoundException::class);
        $action->handleBySlug('non-existent-slug');
    }

    /**
     * Test that the action includes course categories when requested.
     */
    public function test_includes_course_categories_when_requested(): void
    {
        // Create a course with categories
        $course = Course::create([
            'title' => 'Course with Categories',
            'slug' => 'course-with-categories',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Create and attach categories
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $course->categories()->attach([$category1->id, $category2->id]);

        $action = new GetCourse();
        $retrievedCourse = $action->handle($course->id, ['categories']);

        $this->assertTrue($retrievedCourse->relationLoaded('categories'));
        $this->assertEquals(2, $retrievedCourse->categories->count());
        $this->assertTrue($retrievedCourse->categories->contains($category1->id));
        $this->assertTrue($retrievedCourse->categories->contains($category2->id));
    }


    /**
     * Test that the action includes enrollments when requested.
     */
    public function test_includes_enrollments_when_requested(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Course with Enrollments',
            'slug' => 'course-with-enrollments',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $action = new GetCourse();
        $retrievedCourse = $action->handle($course->id, ['enrollments']);

        $this->assertTrue($retrievedCourse->relationLoaded('enrollments'));
    }

    /**
     * Test that the action includes employees when requested.
     */
    public function test_includes_employees_when_requested(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Course with Employees',
            'slug' => 'course-with-employees',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $action = new GetCourse();
        $retrievedCourse = $action->handle($course->id, ['employees']);

        $this->assertTrue($retrievedCourse->relationLoaded('employees'));
    }

    /**
     * Test that the action does not retrieve soft deleted courses.
     */
    public function test_does_not_retrieve_soft_deleted_courses(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Deleted Course',
            'slug' => 'deleted-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Soft delete the course
        $course->delete();

        $action = new GetCourse();

        // Should throw exception when trying to retrieve by ID
        $this->expectException(CourseNotFoundException::class);
        $action->handle($course->id);
    }

    /**
     * Test that the action can retrieve soft deleted courses when specified.
     */
    public function test_can_retrieve_soft_deleted_courses_when_specified(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Deleted Course',
            'slug' => 'deleted-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Soft delete the course
        $course->delete();

        $action = new GetCourse();
        $retrievedCourse = $action->handle($course->id, [], true); // true for withTrashed

        $this->assertInstanceOf(Course::class, $retrievedCourse);
        $this->assertEquals($course->id, $retrievedCourse->id);
        $this->assertEquals('Deleted Course', $retrievedCourse->title);
        $this->assertNotNull($retrievedCourse->deleted_at);
    }
}
