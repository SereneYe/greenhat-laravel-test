<?php

namespace Modules\Employee\Actions\Registration;

use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\Registration\EmployeeRegistrationData;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class CreateEmployeeProfileAction
{
    use AsAction;

    /**
     * Create an employee profile for a user
     *
     * @param User $user The user to create an employee profile for
     * @param EmployeeRegistrationData $data The data to create the employee profile with
     * @return Employee The created employee profile
     */
    public function handle(User $user, EmployeeRegistrationData $data): Employee
    {
        // Create the employee profile
        $employee = Employee::create([
            'user_id' => $user->id,
            'role' => $data->role,
            'highest_qualification' => $data->highestQualification,
            'desired_salary' => $data->desiredSalary,
            'note' => $data->note,
        ]);

        return $employee;
    }
}
