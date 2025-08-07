<?php

namespace Modules\Employee\Observers;

use Modules\Employee\Actions\Registration\SendEmployeeConfirmationEmail;
use App\Services\EmployeeMailService;
use Modules\Employee\Models\Employee;
use Modules\User\Actions\User\AssignRoleToUser;

class EmployeeObserver
{
    public function saved(Employee $model): void
    {
        if ($model->user?->isNotA('employee')) {
            AssignRoleToUser::make()->handle($model->user, 'employee');
        }
    }

    public function created(Employee $model): void
    {
        // Generate a random password for the employee
        $password = \Illuminate\Support\Str::password(12);

        // Use the SendEmployeeConfirmationEmail action to send the confirmation email
        SendEmployeeConfirmationEmail::make()->handle($model, $password);
    }
}
