<?php

namespace Modules\User\Actions\User;

use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Data\User\UpdateUserData;
use Modules\User\Models\User;

class UpdateUser
{
    use AsAction;

    public function handle(UpdateUserData $data): User
    {
        $data->user
            ->fill(
                $data->only('firstName', 'lastName', 'email')
                    ->toArray()
            );

        if ($data->has('password')) {
            $data->user->fill([
                'password' => Hash::make($data->password)
            ]);
        }

        $data->user->save();

        return $data->user;
    }
}
