<?php

namespace Modules\User\Actions\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Data\Auth\RegisterUserData;
use Modules\User\Models\User;
use Modules\User\Exceptions\UserException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class RegisterUserAccount
{
    use AsAction;

    public function handle(RegisterUserData $data): array
    {
        $this->validateVerificationCode($data->email, $data->verificationCode);

        $user = User::create([
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'name' => $data->firstName . ' ' . $data->lastName,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        // generate token
        $token = $user->createToken('user-registration')->plainTextToken;

        // delete verification code from cache
        Cache::forget('verification_code_registration_' . md5($data->email));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function jsonResponse(array $result): JsonResponse
    {
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $result['token'],
            'user' => [
                'id' => $result['user']->id,
                'name' => $result['user']->name,
                'email' => $result['user']->email,
                'first_name' => $result['user']->first_name,
                'last_name' => $result['user']->last_name,
            ]
        ], 201);
    }

    private function validateVerificationCode(string $email, ?string $code): void
    {
        if (!$code) {
            throw UserException::verificationCodeRequired();
        }

        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            throw UserException::invalidVerificationCode();
        }

        $cacheKey = 'verification_code_registration_' . md5($email);
        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode) {
            throw UserException::verificationCodeExpired();
        }

        if ((string)$cachedCode !== (string)$code) {
            throw UserException::invalidVerificationCode();
        }
    }
}
