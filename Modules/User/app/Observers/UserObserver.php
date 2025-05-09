<?php

namespace Modules\User\Observers;

use Modules\User\Models\User;

class UserObserver
{
    public function saving(User $user): User
    {
        $user->name = trim($user->first_name . ' ' . $user->last_name);

        return $user;
    }
}
