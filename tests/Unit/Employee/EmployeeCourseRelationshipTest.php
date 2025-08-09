<?php

namespace Tests\Unit\Employee;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Employee\Models\Employee;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Models\CourseEnrollment;
use Modules\User\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeCourseRelationshipTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $employee;
    private $category;
    private $course;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->employee = Employee::create([
            'user_id' => $this->user->id,
            'role' => 'Developer',
        ]);

        $this->category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
        ]);

        $this->course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test course description',
            'duration_hours' => 10,
            'level' => 'beginner',
        ]);
    }

    /** @test */
    public function test_employee_has_course_enrollments_relationship()
    {
        $this->assertTrue(
            method_exists($this->employee, 'courseEnrollments'),
            'Employee should have courseEnrollments method'
        );

        $relation = $this->employee->courseEnrollments();
        $this->assertInstanceOf(HasMany::class, $relation, 'courseEnrollments should return HasMany relationship');
    }

    /** @test */
    public function test_employee_has_courses_relationship()
    {
        $this->assertTrue(
            method_exists($this->employee, 'courses'),
            'Employee should have courses method'
        );

        $relation = $this->employee->courses();
        $this->assertInstanceOf(BelongsToMany::class, $relation, 'courses should return BelongsToMany relationship');
    }

    /** @test */
    public function test_employee_can_enroll_in_course()
    {
        $this->assertTrue(
            method_exists($this->employee, 'enrollInCourse'),
            'Employee should have enrollInCourse method'
        );

        $enrollment = $this->employee->enrollInCourse($this->course);

        $this->assertInstanceOf(CourseEnrollment::class, $enrollment);
        $this->assertEquals($this->employee->id, $enrollment->employee_id);
        $this->assertEquals($this->course->id, $enrollment->course_id);
        $this->assertNotNull($enrollment->enrolled_at);
        $this->assertNull($enrollment->cancelled_at);

        // Verify it's in database
        $this->assertDatabaseHas('course_enrollments', [
            'employee_id' => $this->employee->id,
            'course_id' => $this->course->id,
        ]);
    }

    /** @test */
    public function test_employee_cannot_enroll_twice_in_same_course()
    {
        $this->assertTrue(
            method_exists($this->employee, 'enrollInCourse'),
            'Employee should have enrollInCourse method'
        );

        $enrollment1 = $this->employee->enrollInCourse($this->course);
        $enrollment2 = $this->employee->enrollInCourse($this->course);

        $this->assertEquals($enrollment1->id, $enrollment2->id, 'Should return existing enrollment');

        // Should only be one enrollment in database
        $count = CourseEnrollment::where([
            'employee_id' => $this->employee->id,
            'course_id' => $this->course->id,
        ])->count();

        $this->assertEquals(1, $count, 'Should only have one enrollment record');
    }

    /** @test */
    public function test_employee_is_enrolled_in_course_check()
    {
        $this->assertTrue(
            method_exists($this->employee, 'isEnrolledIn'),
            'Employee should have isEnrolledIn method'
        );

        // Initially not enrolled
        $this->assertFalse($this->employee->isEnrolledIn($this->course));

        // Enroll
        $this->employee->enrollInCourse($this->course);
        $this->assertTrue($this->employee->isEnrolledIn($this->course));

        // Cancel enrollment
        $enrollment = CourseEnrollment::where([
            'employee_id' => $this->employee->id,
            'course_id' => $this->course->id,
        ])->first();
        $enrollment->update(['cancelled_at' => now()]);

        // Should not be considered enrolled after cancellation
        $this->assertFalse($this->employee->fresh()->isEnrolledIn($this->course));
    }

    /** @test */
    public function test_employee_total_learning_hours_calculation()
    {
        $this->assertTrue(
            method_exists($this->employee, 'getTotalLearningHours'),
            'Employee should have getTotalLearningHours method'
        );

        // Initially 0 hours
        $this->assertEquals(0, $this->employee->getTotalLearningHours());

        // Create additional courses
        $course2 = Course::create([
            'title' => 'Test Course 2',
            'slug' => 'test-course-2',
            'description' => 'Test course 2 description',
            'duration_hours' => 15,
            'level' => 'intermediate',
        ]);

        $course3 = Course::create([
            'title' => 'Test Course 3',
            'slug' => 'test-course-3',
            'description' => 'Test course 3 description',
            'duration_hours' => 5,
            'level' => 'advanced',
        ]);

        // Enroll in multiple courses
        $this->employee->enrollInCourse($this->course);  // 10 hours
        $this->employee->enrollInCourse($course2);       // 15 hours
        $this->employee->enrollInCourse($course3);       // 5 hours

        $this->assertEquals(30, $this->employee->getTotalLearningHours());

        // Cancel one course
        $enrollment = CourseEnrollment::where([
            'employee_id' => $this->employee->id,
            'course_id' => $course2->id,
        ])->first();
        $enrollment->update(['cancelled_at' => now()]);

        // Should not count cancelled course
        $this->assertEquals(15, $this->employee->fresh()->getTotalLearningHours());
    }

    /** @test */
    public function test_employee_course_relationships_work_together()
    {
        // Enroll in course
        $this->employee->enrollInCourse($this->course);

        // Test courseEnrollments relationship
        $enrollments = $this->employee->courseEnrollments;
        $this->assertCount(1, $enrollments);
        $this->assertEquals($this->course->id, $enrollments->first()->course_id);

        // Test courses relationship
        $courses = $this->employee->courses;
        $this->assertCount(1, $courses);
        $this->assertEquals($this->course->id, $courses->first()->id);
    }
}
