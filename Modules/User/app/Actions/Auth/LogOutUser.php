<?php

namespace Modules\User\Actions\Auth;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\User\Models\User;

class LogOutUser
{
    use AsAction;

    public function handle(User $user): void
    {
        event(new Logout('api', $user));

        $user->tokens->each->delete();
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle($request->user());
    }

    public function jsonResponse(): JsonResponse
    {
        return response()->json([
            'loggedOut' => true,
        ], 200, [
            'Clear-Site-Data' => '"cache", "cookies", "storage"',
        ]);
    }
}
