<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;

class PasswordResetCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $verificationCode,
        public string $purpose = 'password_reset'
    ) {
        \Log::info('PasswordResetCodeEmail constructor called', [
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
            'code_length' => strlen($this->verificationCode),
            'purpose' => $this->purpose
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        \Log::info('PasswordResetCodeEmail envelope method called');

        return new Envelope(
            subject: '[Greenhat] Password Reset Verification Code',
            tags: ['password-reset', 'verification-code'],
            metadata: [
                'user_id' => $this->user->id,
                'purpose' => $this->purpose,
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        \Log::info('PasswordResetCodeEmail content method called', [
            'user_name' => $this->user->name,
            'template_data_count' => 10
        ]);

        $templateData = [
            'userName' => $this->user->name,
            'firstName' => $this->user->first_name,
            'userEmail' => $this->user->email,
            'verificationCode' => $this->verificationCode,
            'purpose' => $this->purpose,
            'expirationTime' => '15 minutes',
            'companyName' => config('app.name', 'Greenhat'),
            'resetUrl' => url('/reset-password'),
            'supportEmail' => config('mail.from.address', 'support@greenhat.net'),
            'sentAt' => now()->format('F j, Y \a\t g:i A'),
        ];

        \Log::info('Template data prepared', [
            'template_variables' => array_keys($templateData),
            'verification_code_length' => strlen($this->verificationCode)
        ]);

        return new Content(
            view: 'emails.password-reset-code',
            with: $templateData
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
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Password reset code email failed', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'purpose' => $this->purpose,
            'verification_code' => $this->verificationCode,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
