<?php

namespace Modules\User\Actions\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\ActionRequest;
use Modules\User\Models\User;
use Modules\User\Exceptions\UserException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ResetUserPasswordWithCode
{
    use AsAction;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function asController(ActionRequest $request): array
    {
        try {
            Log::debug('Password reset request received', [
                'email' => $request->input('email'),
                'has_code' => $request->has('code'),
                'has_password' => $request->has('password'),
                'request_data' => $request->except(['password']),
            ]);

            $email = $request->input('email');
            $code = $request->input('code');
            $password = $request->input('password');

            if (!$email || !$code || !$password) {
                throw UserException::invalidInput('Email, code, and password are required');
            }

            return $this->handle($email, $code, $password);
        } catch (UserException $e) {
            Log::warning('Password reset failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during password reset', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function handle(string $email, string $code, string $password): array
    {
        // Validate verification code
        $this->validatePasswordResetCode($email, $code);

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw UserException::userNotFound();
        }

        // Revoke all existing tokens
        $user->tokens()->delete();

        // Reset password
        $user->changePassword($password);

        // Generate new token
        $token = $user->createToken('password-reset-token')->plainTextToken;

        // Clear verification code cache
        Cache::forget('verification_code_password_reset_' . md5($email));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function jsonResponse(array $result): JsonResponse
    {
        return response()->json([
            'message' => 'Password reset successfully',
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

    private function validatePasswordResetCode(string $email, ?string $code): void
    {
        Log::debug('Starting password reset code validation', [
            'email' => $email,
            'code_provided' => $code ? 'yes' : 'no'
        ]);

        if (!$code) {
            Log::warning('Password reset code validation failed: code not provided', [
                'email' => $email
            ]);
            throw UserException::verificationCodeRequired();
        }

        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            Log::warning('Password reset code validation failed: invalid code format', [
                'email' => $email,
                'code_length' => strlen($code),
                'is_digit' => ctype_digit($code) ? 'yes' : 'no'
            ]);
            throw UserException::invalidVerificationCode();
        }

        $cacheKey = 'verification_code_password_reset_' . md5($email);
        Log::debug('Checking cache for reset code', [
            'email' => $email,
            'cache_key' => $cacheKey
        ]);

        $cachedCode = Cache::get($cacheKey);

        if (!$cachedCode) {
            Log::warning('Password reset code validation failed: code not found in cache or expired', [
                'email' => $email
            ]);
            throw UserException::verificationCodeExpired();
        }

        Log::debug('Comparing provided code with cached code', [
            'email' => $email,
            'provided_code' => $code,
            'cached_code' => $cachedCode,
            'match' => ((string)$cachedCode === (string)$code) ? 'yes' : 'no'
        ]);

        if ((string)$cachedCode !== (string)$code) {
            Log::warning('Password reset code validation failed: code mismatch', [
                'email' => $email
            ]);
            throw UserException::invalidVerificationCode();
        }

        Log::info('Password reset code validation successful', [
            'email' => $email
        ]);
    }
}
