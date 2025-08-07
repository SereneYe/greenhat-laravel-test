<?php

namespace App\Services;

use App\Mail\EmployeeRegistrationEmail;
use Illuminate\Support\Facades\Mail;
use Modules\User\Models\User;
use Modules\Employee\Models\Employee;

class EmployeeMailService extends BaseMailService
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
            $this->logEmailAttempt('EmployeeMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                get_class($template));

            // Create and send the email
            Mail::to($recipient)->send($template);

            $this->logEmailSuccess('EmployeeMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                get_class($template));

            return true;
        } catch (\Exception $e) {
            $this->logEmailFailure('EmployeeMailService',
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
            $this->logEmailAttempt('EmployeeMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template));

            // Queue the email
            Mail::to($recipient)->queue($template);

            $this->logEmailSuccess('EmployeeMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template));

            return true;
        } catch (\Exception $e) {
            $this->logEmailFailure('EmployeeMailService',
                is_object($recipient) ? $recipient->email : $recipient,
                'queued:' . get_class($template),
                $e);

            return false;
        }
    }

    /**
     * Send employee registration confirmation email asynchronously
     *
     * @param User $user
     * @param Employee $employee
     * @param string $generatedPassword
     * @return bool
     */
    public function sendRegistrationConfirmation(User $user, Employee $employee, string $generatedPassword): bool
    {
        try {
            \Illuminate\Support\Facades\Log::info('Queuing employee registration confirmation email', [
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'email' => $user->email
            ]);

            // Use queue job to send email asynchronously
            \App\Jobs\SendEmployeeRegistrationEmailJob::dispatch($employee, $generatedPassword);

            \Illuminate\Support\Facades\Log::info('Employee registration email queued successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return true;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue employee registration email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Resend employee registration confirmation email
     *
     * @param Employee $employee
     * @param string $newPassword
     * @return bool
     */
    public function resendRegistrationConfirmation(Employee $employee, string $newPassword): bool
    {
        return $this->sendRegistrationConfirmation($employee->user, $employee, $newPassword);
    }
}
