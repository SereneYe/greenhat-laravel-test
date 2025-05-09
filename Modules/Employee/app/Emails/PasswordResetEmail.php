<?php

namespace Modules\Employee\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $redirect,
    ) {}

    public function build(): self
    {
        return $this
            ->to($this->user->email, $this->user->name)
            ->view('user::emails.reset-password')
            ->subject('[Greenhat] - Reset Password')
            ->with([
                'redirectUrl' => $this->resetPasswordLink(),
            ]);
    }

    protected function resetPasswordLink(): string
    {
        $query = http_build_query([
            'token' => app(PasswordBroker::class)->createToken($this->user),
            'email' => $this->user->email,
        ]);

        return $this->redirect.'?'.$query;
    }
}
