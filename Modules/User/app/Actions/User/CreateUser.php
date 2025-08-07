<?php

namespace Modules\User\Actions\User;

use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Data\User\CreateUserData;
use Modules\User\Models\User;

class CreateUser
{
    use AsAction;

    public function handle(CreateUserData $data): User
    {
        return User::create([
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'name' => $data->firstName . ' ' . $data->lastName,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);
    }
}
