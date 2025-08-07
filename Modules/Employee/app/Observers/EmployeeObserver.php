<?php

namespace Modules\Employee\Observers;

use Modules\Employee\Actions\Email\SendEmployeeConfirmationEmail;
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
        // Dispatch the job to send confirmation email
        SendEmployeeConfirmationEmail::dispatch($model);
    }
}
