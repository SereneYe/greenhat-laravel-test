<?php

namespace App\Jobs;

use App\Mail\CourseEnrollmentConfirmationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Course\Models\CourseEnrollment;

class SendCourseEnrollmentConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    public function __construct(
        public CourseEnrollment $enrollment
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the queue job
     */
    public function handle(): void
    {
        try {
            Log::info('Starting course enrollment confirmation email job', [
                'enrollment_id' => $this->enrollment->id,
                'employee_id' => $this->enrollment->employee_id,
                'course_id' => $this->enrollment->course_id,
                'email' => $this->enrollment->employee->user->email
            ]);

            // Load relationships to avoid N+1 queries
            $this->enrollment->load(['course.categories', 'employee.user']);

            // Send the email
            Mail::to($this->enrollment->employee->user)
                ->send(new CourseEnrollmentConfirmationEmail($this->enrollment));

            Log::info('Course enrollment confirmation email sent successfully', [
                'enrollment_id' => $this->enrollment->id,
                'email' => $this->enrollment->employee->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Course enrollment confirmation email job failed', [
                'enrollment_id' => $this->enrollment->id,
                'email' => $this->enrollment->employee->user->email ?? 'unknown',
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e; // Rethrow to trigger retry
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Course enrollment confirmation email job failed permanently', [
            'enrollment_id' => $this->enrollment->id,
            'email' => $this->enrollment->employee->user->email ?? 'unknown',
            'error' => $exception->getMessage(),
            'total_attempts' => $this->attempts()
        ]);
    }
}
