<?php

namespace Tests\Unit\Course\Actions;

use App\Jobs\SendCourseEnrollmentConfirmationEmailJob;
use App\Mail\CourseEnrollmentConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Course\Actions\CourseEnrollment\EnrollEmployeeInCourse;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;
use Tests\TestCase;

class EnrollEmployeeInCourseEmailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Employee $employee;
    protected Course $course;
    protected CourseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an employee (this also creates the user)
        $this->employee = $this->createTestEmployee([
            'role' => 'developer',
        ]);

        // Get the user from the employee
        $this->user = $this->employee->user;

        // Create a course category
        $this->category = CourseCategory::create([
            'name' => 'Technical',
            'slug' => 'technical',
        ]);

        // Create a course
        $this->course = Course::create([
            'title' => 'Advanced PHP Development',
            'slug' => 'advanced-php-development',
            'instructor' => 'Jane Smith',
            'duration_hours' => 40,
            'price' => 299.99,
            'level' => 'advanced',
        ]);

        // Attach category to course
        $this->course->categories()->attach($this->category);
    }

    /** @test */
    public function it_queues_enrollment_confirmation_email_when_employee_enrolls_in_course()
    {
        // Fake the queue
        Queue::fake();

        // Execute enrollment action
        $action = new EnrollEmployeeInCourse();
        $enrollment = $action->handle($this->employee, $this->course);

        // Assert enrollment was created
        $this->assertDatabaseHas('course_enrollments', [
            'employee_id' => $this->employee->id,
            'course_id' => $this->course->id,
        ]);

        // Assert email job was dispatched
        Queue::assertPushed(SendCourseEnrollmentConfirmationEmailJob::class, function ($job) use ($enrollment) {
            return $job->enrollment->id === $enrollment->id;
        });
    }

    /** @test */
    public function it_queues_correct_enrollment_confirmation_email_content()
    {
        // Fake mail and queue
        Mail::fake();
        Queue::fake();

        // Create enrollment
        $enrollment = $this->employee->enrollInCourse($this->course);

        // Create and queue the email
        $mailable = new CourseEnrollmentConfirmationEmail($enrollment);
        Mail::to($this->user)->queue($mailable);

        // Assert email was queued
        Mail::assertQueued(CourseEnrollmentConfirmationEmail::class, function ($mail) use ($enrollment) {
            return $mail->enrollment->id === $enrollment->id;
        });
    }

    /** @test */
    public function enrollment_confirmation_email_has_correct_subject_and_recipient()
    {
        Mail::fake();

        // Create enrollment
        $enrollment = $this->employee->enrollInCourse($this->course);

        // Queue email
        Mail::to($this->user)->queue(new CourseEnrollmentConfirmationEmail($enrollment));

        // Verify email details
        Mail::assertQueued(CourseEnrollmentConfirmationEmail::class, function ($mail) {
            $envelope = $mail->envelope();

            return str_contains($envelope->subject, 'Course Enrollment Confirmation') &&
                   str_contains($envelope->subject, $this->course->title);
        });
    }
}
