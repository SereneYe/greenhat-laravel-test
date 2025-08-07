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

class EmployeeRegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Employee $employee,
        public string $generatedPassword
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Greenhat] Welcome! Your Employee Account Has Been Created',
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
}
