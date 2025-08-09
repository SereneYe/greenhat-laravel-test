<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Course\Models\CourseEnrollment;

class CourseEnrollmentConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Queue configuration
    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    /**
     * Create a new message instance.
     */
    public function __construct(
        public CourseEnrollment $enrollment
    ) {
        // Set queue and delay
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(2)); // Small delay
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Greenhat] Course Enrollment Confirmation - ' . $this->enrollment->course->title,
            tags: ['course-enrollment', 'confirmation'],
            metadata: [
                'enrollment_id' => $this->enrollment->id,
                'course_id' => $this->enrollment->course->id,
                'employee_id' => $this->enrollment->employee->id,
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-enrollment-confirmation',
            with: [
                'employeeName' => $this->enrollment->employee->user->name,
                'firstName' => $this->enrollment->employee->user->first_name,
                'lastName' => $this->enrollment->employee->user->last_name,
                'employeeEmail' => $this->enrollment->employee->user->email,
                'courseTitle' => $this->enrollment->course->title,
                'courseDescription' => $this->enrollment->course->description,
                'courseInstructor' => $this->enrollment->course->instructor,
                'courseDuration' => $this->enrollment->course->duration_hours,
                'coursePrice' => $this->enrollment->course->price,
                'courseLevel' => $this->enrollment->course->level,
                'courseCategories' => $this->enrollment->course->categories,
                'enrolledAt' => $this->enrollment->enrolled_at->format('F j, Y \a\t g:i A'),
                'companyName' => config('app.name', 'Greenhat'),
                'dashboardUrl' => url('/dashboard'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Handle a failed email sending attempt
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Course enrollment confirmation email failed permanently', [
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course->id,
            'employee_id' => $this->enrollment->employee->id,
            'email' => $this->enrollment->employee->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}
