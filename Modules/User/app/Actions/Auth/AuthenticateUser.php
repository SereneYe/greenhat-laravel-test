<?php

namespace Modules\User\Actions\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Data\Auth\AuthenticateUserData;
use Modules\User\Models\User;
use Modules\User\Transformers\UserTransformer;

class AuthenticateUser
{
    use AsAction;

    public function handle(AuthenticateUserData $data): User
    {
        $this->ensureIsNotRateLimited($data);

        $inputs = [
            'email' => $data->email,
            'password' => $data->password
        ];

        if (! Auth::attempt($inputs)) {
            RateLimiter::hit($this->throttleKey($data));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($data));

        $user = Auth::user();

        return $user;
    }

    public function ensureIsNotRateLimited(AuthenticateUserData $data): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($data), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($data));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(AuthenticateUserData $data): string
    {
        return Str::lower($data->email).'|'.$data->ipAddress;
    }

    public function asController(ActionRequest $request): User
    {
        return $this->handle(AuthenticateUserData::validateAndCreate([
            ...$request->all(),
            'ipAddress' => $request->ip()
        ]));
    }

    public function jsonResponse(User $user): JsonResponse
    {
        $userData = fractal($user, UserTransformer::class)->toArray();

        return response()->json($userData + [
            'accessToken' => $user->createToken('user_login')->plainTextToken
        ]);
    }
}
