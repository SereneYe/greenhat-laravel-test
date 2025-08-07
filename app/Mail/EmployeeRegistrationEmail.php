<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;
use Modules\Employee\Models\Employee;

class EmployeeRegistrationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Queue configuration
    public $tries = 3;           // Number of retries
    public $timeout = 120;       // Timeout in seconds
    public $backoff = [10, 30, 60]; // Retry intervals in seconds

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Employee $employee,
        public string $generatedPassword
    ) {
        // Set queue and delay
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(5)); // Delay 5 seconds
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Greenhat] Welcome! Your Employee Account Has Been Created',
            tags: ['employee-registration', 'welcome'],
            metadata: [
                'user_id' => $this->user->id,
                'employee_id' => $this->employee->id,
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-registration-confirmation',
            with: [
                'userName' => $this->user->name,
                'firstName' => $this->user->first_name,
                'lastName' => $this->user->last_name,
                'userEmail' => $this->user->email,
                'generatedPassword' => $this->generatedPassword,
                'employeeRole' => $this->employee->role,
                'registrationDate' => $this->employee->created_at->format('F j, Y \a\t g:i A'),
                'companyName' => config('app.name', 'Greenhat'),
                'loginUrl' => url('/login'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
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
        \Log::error('Employee registration email failed permanently', [
            'user_id' => $this->user->id,
            'employee_id' => $this->employee->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Optional: Update database status or notify administrators
        // $this->employee->update(['email_failed' => true]);
    }
}
