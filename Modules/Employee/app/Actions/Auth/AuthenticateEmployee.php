<?php

namespace Modules\Employee\Actions\Auth;

use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Exceptions\EmployeeException;
use Modules\User\Actions\Auth\AuthenticateUser;
use Modules\User\Data\Auth\AuthenticateUserData;
use Modules\User\Models\User;

class AuthenticateEmployee
{
    use AsAction;

    public function handle(AuthenticateUserData $data): User
    {
        $user = AuthenticateUser::make()->handle($data);

        if (! $user->employee || $user->isNotAn('employee')) {
            throw EmployeeException::isNotEmployee();
        }

        return $user;
    }

    public function asController(ActionRequest $request): User
    {
        return $this->handle(AuthenticateUserData::validateAndCreate([
            ...$request->all(),
            'ipAddress' => $request->ip(),
        ]));
    }

    public function jsonResponse(User $user): JsonResponse
    {
        return response()->json([
            'accessToken' => $user->createToken('employee_login', ['*'], now()->addDays(7)->endOfDay())->plainTextToken,
        ]);
    }
}
