<?php

namespace Modules\Employee\Actions\Registration;

use App\Services\EmployeeMailService;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Models\Employee;
use Illuminate\Support\Facades\Log;

class SendEmployeeConfirmationEmail
{
    use AsAction;

    /**
     * Execute the action to send a confirmation email to the employee.
     *
     * @param Employee $employee The employee to send the confirmation email to
     * @param string $generatedPassword The auto-generated password for the employee
     * @return bool Whether the email was sent successfully
     */
    public function handle(Employee $employee, string $generatedPassword): bool
    {
        try {
            // Use the centralized EmployeeMailService
            $mailService = new EmployeeMailService();
            return $mailService->sendRegistrationConfirmation(
                $employee->user,
                $employee,
                $generatedPassword
            );
        } catch (\Exception $e) {
            Log::error('SendEmployeeConfirmationEmail action failed', [
                'employee_id' => $employee->id,
                'user_id' => $employee->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
