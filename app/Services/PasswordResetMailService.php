<?php

namespace App\Services;

use App\Mail\PasswordResetCodeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\User\Models\User;

class PasswordResetMailService extends BaseMailService
{
    /**
     * Send email notification
     *
     * @param mixed $recipient
     * @param mixed $template
     * @param array $data
     * @return bool
     */
    public function send($recipient, $template, array $data = []): bool
    {
        try {
            $this->logEmailAttempt('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                get_class($template));

            // Create and send the email
            Mail::to($recipient)->send($template);

            $this->logEmailSuccess('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                get_class($template));

            return true;
        } catch (\Exception $e) {
            $this->logEmailFailure('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                get_class($template),
                $e);

            return false;
        }
    }

    /**
     * Queue email notification
     *
     * @param mixed $recipient
     * @param mixed $template
     * @param array $data
     * @return bool
     */
    public function queue($recipient, $template, array $data = []): bool
    {
        try {
            $this->logEmailAttempt('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template));

            // Queue the email
            Mail::to($recipient)->queue($template);

            $this->logEmailSuccess('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template));

            return true;
        } catch (\Exception $e) {
            $this->logEmailFailure('PasswordResetMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template),
                $e);

            return false;
        }
    }
    /**
     * Send password reset verification code email
     *
     * @param User $user
     * @param string $verificationCode
     * @param string $purpose
     * @return bool
     */
    public function sendPasswordResetCode(User $user, string $verificationCode, string $purpose = 'password_reset'): bool
    {
        Log::info('Starting sendPasswordResetCode', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'code_length' => strlen($verificationCode),
            'purpose' => $purpose
        ]);

        // Create the email instance
        $email = new PasswordResetCodeEmail($user, $verificationCode, $purpose);

        // Use the standardized send method
        return $this->send($user, $email);
    }

    /**
     * Generate and send verification code for password reset
     *
     * @param string $email
     * @param string $purpose
     * @return bool
     */
    public function generateAndSendVerificationCode(string $email, string $purpose = 'password_reset'): bool
    {
        try {
            // Find user by email
            $user = User::where('email', $email)->first();
            if (!$user) {
                Log::warning('Password reset requested for non-existent user', [
                    'email' => $email,
                    'purpose' => $purpose
                ]);
                return false;
            }

            // Generate 6-digit verification code
            $verificationCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Store in cache for 15 minutes
            $cacheKey = 'verification_code_' . $purpose . '_' . md5($email);
            Cache::put($cacheKey, $verificationCode, now()->addMinutes(15));

            // Send email with verification code
            return $this->sendPasswordResetCode($user, $verificationCode, $purpose);

        } catch (\Exception $e) {
            $this->logEmailFailure('PasswordResetMailService', $email, 'verification_code', $e);
            return false;
        }
    }

    /**
     * Verify if a verification code is valid
     *
     * @param string $email
     * @param string $code
     * @param string $purpose
     * @return bool
     */
    public function verifyCode(string $email, string $code, string $purpose = 'password_reset'): bool
    {
        $cacheKey = 'verification_code_' . $purpose . '_' . md5($email);
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode) {
            return false;
        }

        return (string)$cachedCode === (string)trim($code);
    }

    /**
     * Clear verification code from cache
     *
     * @param string $email
     * @param string $purpose
     * @return void
     */
    public function clearVerificationCode(string $email, string $purpose = 'password_reset'): void
    {
        $cacheKey = 'verification_code_' . $purpose . '_' . md5($email);
        Cache::forget($cacheKey);

        Log::info('Verification code cleared from cache', [
            'email' => $email,
            'purpose' => $purpose,
            'cache_key' => $cacheKey
        ]);
    }
}
