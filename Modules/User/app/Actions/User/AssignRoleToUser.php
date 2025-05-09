<?php

namespace Modules\User\Actions\User;

use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Models\User;
use Silber\Bouncer\Bouncer;

class AssignRoleToUser
{
    use AsAction;

    public function handle(User $user, string $role): User
    {
        $user->assign($role);

        Bouncer::create()->refreshFor($user);

        return $user;
    }
}
