<?php

namespace Modules\Auth\Actions\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SendVerificationCode
{
    use AsAction;

    /**
     * The time in seconds that the verification code is valid.
     */
    private const EXPIRATION_TIME = 600; // 10 minutes

    /**
     * The time in seconds that the user must wait before requesting another code.
     */
    private const THROTTLE_TIME = 60; // 60 seconds

    /**
     * Handle the action.
     */
    public function handle(array $data): array
    {
        // Validate the email
        $validator = Validator::make($data, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors(),
            ];
        }

        $email = $data['email'];

        // Check if a code was recently sent (throttling)
        $throttleKey = 'verification_throttle_' . md5($email);
        if (Cache::has($throttleKey)) {
            $timeRemaining = Cache::get($throttleKey);
            return [
                'success' => false,
                'message' => "Please wait {$timeRemaining} seconds before requesting another code",
                'time_remaining' => $timeRemaining,
            ];
        }

        // Generate a 6-digit verification code
        $code = mt_rand(100000, 999999);

        // Store the code in cache with expiration
        $cacheKey = 'verification_code_' . md5($email);
        Cache::put($cacheKey, $code, self::EXPIRATION_TIME);

        // Set throttle to prevent frequent requests
        Cache::put($throttleKey, self::THROTTLE_TIME, self::THROTTLE_TIME);

        // In a production environment, we would send an email here
        // For development, we'll log the code
        Log::info("Verification code for {$email}: {$code}");

        return [
            'success' => true,
            'message' => 'Verification code sent successfully',
            'debug_code' => $code, // Only for development
        ];
    }

    /**
     * Handle the action as a controller.
     */
    public function asController(): JsonResponse
    {
        $data = request()->only('email');
        $result = $this->handle($data);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
