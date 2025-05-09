<?php

namespace Modules\User\Actions\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Emails\PasswordResetEmail;
use Modules\User\Models\User;

class SendUserResetPasswordLink
{
    use AsAction;

    public function handle(User $user, string $redirect): bool
    {
        try {
            Mail::send(new PasswordResetEmail($user, $redirect));

            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'redirect' => 'required|url',
        ];
    }

    public function asController(ActionRequest $request): bool
    {
        return $this->handle(User::query()->where('email', $request->input('email'))->firstOrFail(),
            $request->input('redirect'));
    }

    public function jsonResponse(bool $sent): JsonResponse
    {
        return response()->json([
            'passwordResetSent' => $sent,
        ]);
    }
}
