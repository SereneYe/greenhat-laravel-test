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

    /**
     * Authenticate user for web application with token generation
     *
     * @param AuthenticateUserData $data The authentication data
     * @return array<string, mixed> Array containing user and token
     */
    public function authenticateWithToken(AuthenticateUserData $data): array
    {
        $user = $this->handle($data);

        // Generate web auth token
        $token = $user->createToken('web-auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * JSON response for web authentication
     *
     * @param array<string, mixed> $result The authentication result
     * @return JsonResponse The JSON response
     */
    public function webJsonResponse(array $result): JsonResponse
    {
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $result['token'],
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'first_name' => $result['user']->first_name,
                'last_name' => $result['user']->last_name,
            ]
        ]);
    }

    /**
     * Handle the authentication process
     *
     * @param AuthenticateUserData $data The authentication data
     * @return User The authenticated user
     * @throws ValidationException When authentication fails
     */
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

    /**
     * Ensure the login request is not rate limited
     *
     * @param AuthenticateUserData $data The authentication data
     * @return void
     * @throws ValidationException When too many attempts
     */
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

    /**
     * Get the throttle key for the request
     *
     * @param AuthenticateUserData $data The authentication data
     * @return string The throttle key
     */
    public function throttleKey(AuthenticateUserData $data): string
    {
        return Str::lower($data->email).'|'.$data->ipAddress;
    }

    /**
     * Handle the request as a controller action
     *
     * @param ActionRequest $request The request
     * @return User The authenticated user
     */
    public function asController(ActionRequest $request): User
    {
        return $this->handle(AuthenticateUserData::validateAndCreate([
            ...$request->all(),
            'ipAddress' => $request->ip()
        ]));
    }

    /**
     * Format the response as JSON
     *
     * @param User $user The authenticated user
     * @return JsonResponse The JSON response
     */
    public function jsonResponse(User $user): JsonResponse
    {
        $userData = fractal($user, UserTransformer::class)->toArray();

        return response()->json($userData + [
            'accessToken' => $user->createToken('user_login')->plainTextToken
        ]);
    }
}
