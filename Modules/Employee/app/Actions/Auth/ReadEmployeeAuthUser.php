<?php

namespace Modules\Employee\Actions\Auth;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsController;
use Modules\Employee\Exceptions\EmployeeException;
use Modules\User\Models\User;
use Modules\User\Transformers\UserTransformer;
use Spatie\Fractal\Fractal;

class ReadEmployeeAuthUser
{
    use AsController;

    public function asController(ActionRequest $request): User
    {
        $user = $request->user();

        if (! $user->employee || $user->isNotAn('employee')) {
            throw EmployeeException::isNotEmployee();
        }

        return $user;
    }

    public function jsonResponse(User $data): Fractal
    {
        return fractal($data, UserTransformer::class)
            ->parseIncludes('employee');
    }
}
