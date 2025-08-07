<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Contracts\MailServiceInterface;

abstract class BaseMailService implements MailServiceInterface
{
    /**
     * Log email sending attempt
     */
    protected function logEmailAttempt(string $service, string $email, string $type): void
    {
        Log::info("Email sending initiated", [
            'service' => $service,
            'recipient' => $email,
            'type' => $type,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log email sending success
     */
    protected function logEmailSuccess(string $service, string $email, string $type): void
    {
        Log::info("Email sent successfully", [
            'service' => $service,
            'recipient' => $email,
            'type' => $type,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log email sending failure
     */
    protected function logEmailFailure(string $service, string $email, string $type, \Exception $e): void
    {
        Log::error("Email sending failed", [
            'service' => $service,
            'recipient' => $email,
            'type' => $type,
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ]);
    }
}
