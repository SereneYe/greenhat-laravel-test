<?php

namespace App\Jobs;

use App\Mail\PasswordResetCodeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\User\Models\User;

class SendPasswordResetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public User $user,
        public string $verificationCode,
        public string $purpose = 'password_reset'
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            Log::info('Starting password reset email job', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'purpose' => $this->purpose
            ]);

            Mail::to($this->user)
                ->send(new PasswordResetCodeEmail(
                    $this->user,
                    $this->verificationCode,
                    $this->purpose
                ));

            Log::info('Password reset email sent successfully', [
                'user_id' => $this->user->id,
                'email' => $this->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset email job failed', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Password reset email job failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}
