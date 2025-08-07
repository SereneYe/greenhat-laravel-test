<?php

namespace Modules\Employee\Observers;

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

    public function creating(Employee $model): void
    {
        // Log employee creation
        \Illuminate\Support\Facades\Log::info('Employee being created', [
            'email' => $model->user->email ?? 'unknown'
        ]);
    }

    public function created(Employee $model): void
    {
        \Illuminate\Support\Facades\Log::info('Employee created, preparing to send welcome email', [
            'employee_id' => $model->id,
            'user_id' => $model->user_id,
            'email' => $model->user->email
        ]);

        // Generate a random password for the employee
        $password = \Illuminate\Support\Str::password(12);

        // Use queue job to send email asynchronously
        \App\Jobs\SendEmployeeRegistrationEmailJob::dispatch($model, $password);

        \Illuminate\Support\Facades\Log::info('Employee registration email job dispatched', [
            'employee_id' => $model->id,
            'email' => $model->user->email
        ]);
    }
}
