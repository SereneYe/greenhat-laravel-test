<?php

namespace Modules\User\Actions\User;

use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Data\User\CreateUserData;
use Modules\User\Models\User;

class CreateUser
{
    use AsAction;

    public function handle(CreateUserData $data): User
    {
        return User::create(
            $data->only('firstName', 'lastName', 'email', 'password')
                ->toArray()
        );
    }
}
