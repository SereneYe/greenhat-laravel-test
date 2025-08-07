<?php

namespace Modules\User\Actions\Auth;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SendUserVerificationCode
{
    use AsAction;

    private const EXPIRATION_TIME = 600; // 10 minutes
    private const THROTTLE_TIME = 60; // 60 seconds

    public function handle(string $email, string $purpose = 'registration'): array
    {
        // 验证邮箱格式
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

        // 根据目的检查用户存在性
        if ($purpose === 'password_reset') {
            $user = \Modules\User\Models\User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'No account found with this email address',
                ];
            }
        }

        // 检查节流限制
        $throttleKey = "verification_throttle_{$purpose}_" . md5($email);
        if (Cache::has($throttleKey)) {
            $timeRemaining = Cache::get($throttleKey);
            return [
                'success' => false,
                'message' => "Please wait {$timeRemaining} seconds before requesting another code",
                'time_remaining' => $timeRemaining,
            ];
        }

        // 生成6位验证码
        $code = str_pad((string)mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        // 存储验证码
        $cacheKey = "verification_code_{$purpose}_" . md5($email);
        Cache::put($cacheKey, $code, self::EXPIRATION_TIME);

        // 设置节流
        Cache::put($throttleKey, self::THROTTLE_TIME, self::THROTTLE_TIME);

        // 记录日志
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

            $result = $this->handle($email, $purpose);

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (\Exception $e) {
            Log::error('Verification code sending failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code',
            ], 500);
        }
    }
}
