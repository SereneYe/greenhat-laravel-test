<?php

namespace App\Jobs;

use App\Mail\EmployeeRegistrationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Employee\Models\Employee;

class SendEmployeeRegistrationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [10, 30, 60];

    public function __construct(
        public Employee $employee,
        public string $password
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the queue job
     */
    public function handle(): void
    {
        try {
            Log::info('Starting employee registration email job', [
                'employee_id' => $this->employee->id,
                'user_id' => $this->employee->user_id,
                'email' => $this->employee->user->email
            ]);

            // Send the email
            Mail::to($this->employee->user)
                ->send(new EmployeeRegistrationEmail(
                    $this->employee->user,
                    $this->employee,
                    $this->password
                ));

            // Update email sent timestamp
            $this->employee->update([
                'confirmation_email_sent_at' => now()
            ]);

            Log::info('Employee registration email sent successfully', [
                'employee_id' => $this->employee->id,
                'email' => $this->employee->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Employee registration email job failed', [
                'employee_id' => $this->employee->id,
                'email' => $this->employee->user->email,
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
        Log::error('Employee registration email job failed permanently', [
            'employee_id' => $this->employee->id,
            'email' => $this->employee->user->email,
            'error' => $exception->getMessage(),
            'total_attempts' => $this->attempts()
        ]);

        // Optional: Mark email sending as failed
        $this->employee->update([
            'email_send_failed' => true,
            'email_failure_reason' => $exception->getMessage()
        ]);
    }
}
