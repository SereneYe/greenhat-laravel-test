<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\Course\DeleteCourse;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Models\CourseEnrollment;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

class DeleteCourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action soft deletes a course.
     */
    public function test_soft_deletes_course(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Course to Delete',
            'slug' => 'course-to-delete',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $action = new DeleteCourse();
        $result = $action->handle($course->id);

        $this->assertTrue($result);

        // The course should not be found in a normal query
        $this->assertNull(Course::find($course->id));

        // But it should be found when including trashed records
        $this->assertNotNull(Course::withTrashed()->find($course->id));

        // Verify in the database
        $this->assertSoftDeleted('courses', [
            'id' => $course->id,
        ]);
    }

    /**
     * Test that the action maintains relationships after deletion.
     */
    public function test_maintains_relationships_after_deletion(): void
    {
        // Create a course with categories
        $course = Course::create([
            'title' => 'Course with Relationships',
            'slug' => 'course-with-relationships',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Create and attach categories
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $course->categories()->attach([$category1->id, $category2->id]);

        // Delete the course
        $action = new DeleteCourse();
        $action->handle($course->id);

        // Refresh the course with trashed records
        $deletedCourse = Course::withTrashed()->find($course->id);

        // The relationships should still be intact
        $this->assertEquals(2, $deletedCourse->categories()->count());
        $this->assertTrue($deletedCourse->categories->contains($category1->id));
        $this->assertTrue($deletedCourse->categories->contains($category2->id));

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
     * Test that the action handles enrollments correctly when deleting a course.
     */
    public function test_handles_enrollments_correctly_when_deleting_course(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Course with Enrollments',
            'slug' => 'course-with-enrollments',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Create employees and enrollments
        $employee1 = $this->createTestEmployee();
        $employee2 = $this->createTestEmployee();

        CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee1->id,
            'enrolled_at' => now(),
        ]);

        CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee2->id,
            'enrolled_at' => now(),
        ]);

        // Delete the course
        $action = new DeleteCourse();
        $action->handle($course->id);

        // Refresh the course with trashed records
        $deletedCourse = Course::withTrashed()->find($course->id);

        // The enrollments should still be intact
        $this->assertEquals(2, $deletedCourse->enrollments()->count());

        // Verify in the database
        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'employee_id' => $employee1->id,
        ]);

        $this->assertDatabaseHas('course_enrollments', [
            'course_id' => $course->id,
            'employee_id' => $employee2->id,
        ]);
    }

    /**
     * Test that the action returns false if the course is already deleted.
     */
    public function test_returns_false_if_course_already_deleted(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Already Deleted Course',
            'slug' => 'already-deleted-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Delete the course first
        $course->delete();

        // Try to delete it again
        $action = new DeleteCourse();
        $result = $action->handle($course->id);

        // Should return false
        $this->assertFalse($result);
    }

    /**
     * Test that the action can force delete a course.
     */
    public function test_can_force_delete_course(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Force Delete Course',
            'slug' => 'force-delete-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Force delete the course
        $action = new DeleteCourse();
        $result = $action->handle($course->id, true); // true for force delete

        $this->assertTrue($result);

        // The course should not be found even when including trashed records
        $this->assertNull(Course::withTrashed()->find($course->id));

        // Verify in the database
        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
    }

    /**
     * Test that the action removes pivot records when force deleting.
     */
    public function test_removes_pivot_records_when_force_deleting(): void
    {
        // Create a course with categories
        $course = Course::create([
            'title' => 'Force Delete with Relationships',
            'slug' => 'force-delete-with-relationships',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Create and attach categories
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $course->categories()->attach([$category1->id, $category2->id]);

        // Force delete the course
        $action = new DeleteCourse();
        $action->handle($course->id, true); // true for force delete

        // Verify pivot records are removed
        $this->assertDatabaseMissing('course_course_category', [
            'course_id' => $course->id,
        ]);
    }
}
