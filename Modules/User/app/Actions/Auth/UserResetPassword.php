<?php

namespace Modules\User\Actions\Auth;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Auth\Exceptions\ResetPasswordException;
use Modules\User\Models\User;

class UserResetPassword
{
    use AsAction;

    public function __construct(protected PasswordBroker $passwordBroker)
    {
    }

    public function handle(string $email, string $password, string $token): bool
    {
        $user = User::whereEmail($email)->firstOrFail();

        if (!$this->passwordBroker->tokenExists($user, $token)) {
            throw ResetPasswordException::invalidToken();
        }

        $user->changePassword($password);

        $this->passwordBroker->deleteToken($user);

        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required',
            'password' => 'required',
            'token' => 'required'
        ];
    }

    public function asController(ActionRequest $request): bool
    {
        return $this->handle(
            $request->input('email'),
            $request->input('password'),
            $request->input('token')
        );
    }

    public function jsonResponse(bool $state): JsonResponse
    {
        return response()->json([
            'resetPassword' => $state
        ]);
    }
}
