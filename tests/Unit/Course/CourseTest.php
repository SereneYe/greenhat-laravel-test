<?php

namespace Tests\Unit\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Models\CourseEnrollment;
use Modules\Employee\Models\Employee;
use Modules\Media\Models\FilamentMediaLibrary;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that course has fillable attributes.
     */
    public function test_course_has_fillable_attributes(): void
    {
        $course = new Course();

        $this->assertContains('title', $course->getFillable());
        $this->assertContains('slug', $course->getFillable());
        $this->assertContains('description', $course->getFillable());
        $this->assertContains('content', $course->getFillable());
        $this->assertContains('instructor', $course->getFillable());
        $this->assertContains('duration_hours', $course->getFillable());
        $this->assertContains('price', $course->getFillable());
        $this->assertContains('level', $course->getFillable());
    }

    /**
     * Test that course casts attributes correctly.
     */
    public function test_course_casts_attributes_correctly(): void
    {
        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $this->assertIsInt($course->duration_hours);
        $this->assertEquals(10, $course->duration_hours);

        // Price should be cast to decimal
        $this->assertIsNumeric($course->price);
        $this->assertEquals(99.99, $course->price);
    }

    /**
     * Test that course uses soft deletes.
     */
    public function test_course_uses_soft_deletes(): void
    {
        $course = Course::create([
            'title' => 'Soft Delete Test',
            'slug' => 'soft-delete-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $course->delete();

        // The course should not be found in a normal query
        $this->assertNull(Course::find($course->id));

        // But it should be found when including trashed records
        $this->assertNotNull(Course::withTrashed()->find($course->id));
    }

    /**
     * Test that course belongs to many categories.
     */
    public function test_course_belongs_to_many_categories(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Relationship Test',
            'slug' => 'relationship-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Check that the categories relationship exists and is the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $course->categories());
    }


    /**
     * Test that course has many enrollments.
     */
    public function test_course_has_many_enrollments(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Enrollments Test',
            'slug' => 'enrollments-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Check that the enrollments relationship exists and is the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $course->enrollments());
    }

    /**
     * Test that course belongs to many employees.
     */
    public function test_course_belongs_to_many_employees(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Employees Test',
            'slug' => 'employees-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Check that the employees relationship exists and is the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $course->employees());
    }

    /**
     * Test by_level scope filters correctly.
     */
    public function test_by_level_scope_filters_correctly(): void
    {
        // Create courses with different levels
        Course::create([
            'title' => 'Beginner Course',
            'slug' => 'beginner-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        Course::create([
            'title' => 'Intermediate Course',
            'slug' => 'intermediate-course',
            'duration_hours' => 20,
            'price' => 199.99,
            'level' => 'intermediate',
        ]);

        Course::create([
            'title' => 'Advanced Course',
            'slug' => 'advanced-course',
            'duration_hours' => 30,
            'price' => 299.99,
            'level' => 'advanced',
        ]);

        // Query using the byLevel scope
        $beginnerCourses = Course::byLevel('beginner')->get();
        $intermediateCourses = Course::byLevel('intermediate')->get();
        $advancedCourses = Course::byLevel('advanced')->get();

        // Should only return courses with the specified level
        $this->assertEquals(1, $beginnerCourses->count());
        $this->assertEquals('Beginner Course', $beginnerCourses->first()->title);

        $this->assertEquals(1, $intermediateCourses->count());
        $this->assertEquals('Intermediate Course', $intermediateCourses->first()->title);

        $this->assertEquals(1, $advancedCourses->count());
        $this->assertEquals('Advanced Course', $advancedCourses->first()->title);
    }

    /**
     * Test by_categories scope filters correctly.
     */
    public function test_by_categories_scope_filters_correctly(): void
    {
        // Create categories
        $category1 = CourseCategory::create([
            'name' => 'Category 1',
            'slug' => 'category-1',
        ]);

        $category2 = CourseCategory::create([
            'name' => 'Category 2',
            'slug' => 'category-2',
        ]);

        // Create courses and attach to categories
        $course1 = Course::create([
            'title' => 'Course 1',
            'slug' => 'course-1',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);
        $course1->categories()->attach($category1->id);

        $course2 = Course::create([
            'title' => 'Course 2',
            'slug' => 'course-2',
            'duration_hours' => 20,
            'price' => 199.99,
            'level' => 'intermediate',
        ]);
        $course2->categories()->attach($category2->id);

        $course3 = Course::create([
            'title' => 'Course 3',
            'slug' => 'course-3',
            'duration_hours' => 30,
            'price' => 299.99,
            'level' => 'advanced',
        ]);
        $course3->categories()->attach([$category1->id, $category2->id]);

        // Query using the byCategories scope
        $category1Courses = Course::byCategories([$category1->id])->get();
        $category2Courses = Course::byCategories([$category2->id])->get();
        $bothCategoriesCourses = Course::byCategories([$category1->id, $category2->id])->get();

        // Should return courses in the specified categories
        $this->assertEquals(2, $category1Courses->count());
        $this->assertTrue($category1Courses->contains('title', 'Course 1'));
        $this->assertTrue($category1Courses->contains('title', 'Course 3'));

        $this->assertEquals(2, $category2Courses->count());
        $this->assertTrue($category2Courses->contains('title', 'Course 2'));
        $this->assertTrue($category2Courses->contains('title', 'Course 3'));

        // When filtering by both categories, should return courses in either category
        $this->assertEquals(3, $bothCategoriesCourses->count());
    }

    /**
     * Test order_by_newest scope sorts correctly.
     */
    public function test_order_by_newest_scope_sorts_correctly(): void
    {
        // Create courses with different creation dates
        $oldCourse = Course::create([
            'title' => 'Old Course',
            'slug' => 'old-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Simulate a delay
        sleep(1);

        $newCourse = Course::create([
            'title' => 'New Course',
            'slug' => 'new-course',
            'duration_hours' => 20,
            'price' => 199.99,
            'level' => 'intermediate',
        ]);

        // Query using the orderByNewest scope
        $courses = Course::orderByNewest()->get();

        // Should return newest courses first
        $this->assertEquals('New Course', $courses->first()->title);
        $this->assertEquals('Old Course', $courses->last()->title);
    }

    /**
     * Test get_enrollment_count returns correct count.
     */
    public function test_get_enrollment_count_returns_correct_count(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Enrollment Count Test',
            'slug' => 'enrollment-count-test',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Create some employees
        $employee1 = $this->createTestEmployee();
        $employee2 = $this->createTestEmployee();
        $employee3 = $this->createTestEmployee();

        // Create enrollments
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

        // Create a cancelled enrollment
        CourseEnrollment::create([
            'course_id' => $course->id,
            'employee_id' => $employee3->id,
            'enrolled_at' => now(),
            'cancelled_at' => now(),
        ]);

        // Should only count non-cancelled enrollments
        $this->assertEquals(2, $course->getEnrollmentCount());
    }
}
