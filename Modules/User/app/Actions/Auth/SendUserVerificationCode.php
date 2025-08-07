<?php

namespace Modules\User\Actions\Auth;

use App\Services\PasswordResetMailService;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SendUserVerificationCode
{
    use AsAction;

    private const EXPIRATION_TIME = 900; // 15 minutes
    private const THROTTLE_TIME = 60; // 60 seconds

    public function handle(string $email, string $purpose = 'registration'): array
    {
        // Validate email format
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // Check user existence based on purpose
        if ($purpose === 'password_reset') {
            $user = \Modules\User\Models\User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'No account found with this email address',
                ];
            }
        }

        // Check throttling
        $throttleKey = "verification_throttle_{$purpose}_" . md5($email);
        if (Cache::has($throttleKey)) {
            $timeRemaining = Cache::get($throttleKey);
            return [
                'success' => false,
                'message' => "Please wait {$timeRemaining} seconds before requesting another code",
                'time_remaining' => $timeRemaining,
            ];
        }

        // Set throttling
        Cache::put($throttleKey, self::THROTTLE_TIME, self::THROTTLE_TIME);

        // For password reset, use the mail service
        if ($purpose === 'password_reset') {
            try {
                Log::info('Using PasswordResetMailService for password reset', [
                    'email' => $email,
                    'purpose' => $purpose
                ]);

                $mailService = new PasswordResetMailService();
                $emailSent = $mailService->generateAndSendVerificationCode($email, $purpose);

                Log::info('PasswordResetMailService result', [
                    'email' => $email,
                    'purpose' => $purpose,
                    'email_sent' => $emailSent
                ]);

                if (!$emailSent) {
                    Log::warning('Failed to send verification code email', [
                        'email' => $email,
                        'purpose' => $purpose
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Failed to send verification code. Please try again later.',
                    ];
                }

                Log::info('Verification code email sent successfully', [
                    'email' => $email,
                    'purpose' => $purpose
                ]);

                return [
                    'success' => true,
                    'message' => 'Verification code sent to your email',
                    'expires_in' => '15 minutes',
                ];
            } catch (\Exception $e) {
                Log::error('Exception in password reset email sending', [
                    'email' => $email,
                    'purpose' => $purpose,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return [
                    'success' => false,
                    'message' => 'An error occurred while sending the verification code. Please try again later.',
                ];
            }
        }

        // For other purposes, use the existing code generation logic
        // Generate 6-digit verification code
        $code = str_pad((string)mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store verification code
        $cacheKey = "verification_code_{$purpose}_" . md5($email);
        Cache::put($cacheKey, $code, self::EXPIRATION_TIME);

        // Log for non-password-reset purposes
        Log::info("{$purpose} verification code for {$email}: {$code}");

        return [
            'success' => true,
            'message' => 'Verification code sent successfully',
            'debug_code' => app()->environment(['local', 'development']) ? $code : null,
        ];
    }

    public function asController(): JsonResponse
    {
        try {
            $email = request()->input('email');
            $purpose = request()->input('purpose', 'registration');

            Log::info('Verification code request received via API', [
                'email' => $email,
                'purpose' => $purpose,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_method' => request()->method(),
                'request_path' => request()->path(),
                'request_url' => request()->fullUrl()
            ]);

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid email format in verification code request', [
                    'email' => $email,
                    'purpose' => $purpose
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email address format.',
                ], 422);
            }

            Log::info('Calling handle method for verification code', [
                'email' => $email,
                'purpose' => $purpose
            ]);

            $result = $this->handle($email, $purpose);

            Log::info('Handle method completed for verification code', [
                'email' => $email,
                'purpose' => $purpose,
                'success' => $result['success'],
                'message' => $result['message']
            ]);

            // For password reset, add additional information in the response
            if ($purpose === 'password_reset' && $result['success']) {
                $result['expires_in'] = '15 minutes';
                $result['reset_url'] = url('/reset-password');

                Log::info('Password reset verification code sent successfully', [
                    'email' => $email,
                    'expires_in' => '15 minutes',
                    'reset_url' => url('/reset-password')
                ]);
            }

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            Log::error('Unexpected exception in verification code sending', [
                'email' => request()->input('email'),
                'purpose' => request()->input('purpose', 'registration'),
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while sending the verification code. Please try again later.',
                'error' => app()->environment(['local', 'development']) ? $e->getMessage() : null
            ], 500);
        }
    }
}
