<?php

namespace Modules\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function updateProfile(User $user, Employee $employee): bool
    {
        return $this->canModify($user, $employee);
    }

    private function canModify(User $user, Employee $employee): bool
    {
        return $user->isA('admin') || (
                $user->isA('employee') && $user->employee->getKey() === $employee->getKey()
            );
    }
}
