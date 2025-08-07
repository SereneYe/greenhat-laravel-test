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
     * Send employee registration confirmation email
     *
     * @param User $user
     * @param Employee $employee
     * @param string $generatedPassword
     * @return bool
     */
    public function sendRegistrationConfirmation(User $user, Employee $employee, string $generatedPassword): bool
    {
        // Create the email instance
        $email = new EmployeeRegistrationEmail($user, $employee, $generatedPassword);

        // Send the email
        $result = $this->send($user, $email);

        // Update confirmation email sent timestamp if successful
        if ($result) {
            $employee->update([
                'confirmation_email_sent_at' => now(),
            ]);
        }

        return $result;
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
