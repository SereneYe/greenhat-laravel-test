<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Modules\User\Actions\Auth\ResetUserPasswordWithCode;
use Modules\User\Actions\Auth\SendUserVerificationCode;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset request form.
     *
     * @return View The forgot password view
     */
    public function create(): View
    {
        return view('auth::forgot-password');
    }

    /**
     * Handle an incoming password reset code request.
     *
     * @param Request $request The request with email information
     * @return RedirectResponse|JsonResponse Redirect back with status or JSON response
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $result = SendUserVerificationCode::run([
                'email' => $request->input('email'),
                'purpose' => 'password_reset'
            ]);

            if ($request->wantsJson()) {
                return response()->json($result);
            }

            if ($result['success']) {
                return back()->with('status', 'Password reset code sent to your email');
            } else {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => $result['message']]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send reset code']);
        }
    }

    /**
     * Display the password reset form.
     *
     * Note: This method is kept for backward compatibility but is not used in the new flow.
     *
     * @param string $token The password reset token
     * @return View The reset password view
     */
    public function edit(string $token): View
    {
        return view('auth::reset-password', ['token' => $token]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param Request $request The request with password reset information
     * @return RedirectResponse|JsonResponse Redirect to login or back with errors, or JSON response
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        // If the request has a 'code' parameter, use the new verification code flow
        if ($request->has('code')) {
            try {
                $response = ResetUserPasswordWithCode::run([
                    'email' => $request->email,
                    'code' => $request->code,
                    'password' => $request->password,
                    'password_confirmation' => $request->password_confirmation
                ]);

                if ($request->wantsJson()) {
                    return response()->json($response);
                }

                if ($response['success']) {
                    // Store token in session for web flow
                    if (isset($response['token'])) {
                        session(['api_token' => $response['token']]);
                    }

                    return redirect()->route('login')
                        ->with('status', 'Password has been reset successfully');
                } else {
                    return back()->withInput($request->only('email'))
                        ->withErrors(['email' => $response['message']]);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['email' => $e->getMessage()]);
            }
        }

        // Legacy token-based flow (kept for backward compatibility)
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
