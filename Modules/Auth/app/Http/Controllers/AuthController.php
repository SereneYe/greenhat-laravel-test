<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\User\Actions\Auth\AuthenticateUser;
use Modules\User\Actions\Auth\RegisterUserAccount;
use Modules\User\Data\Auth\AuthenticateUserData;
use Modules\User\Data\Auth\RegisterUserData;
use Modules\User\Exceptions\UserException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return View The login view
     */
    public function showLoginForm(): View
    {
        return view('auth::login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request The login request
     * @return RedirectResponse Redirect to dashboard or back with errors
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $authData = AuthenticateUserData::validateAndCreate([
                'email' => $request->email,
                'password' => $request->password,
                'remember' => $request->boolean('remember', false),
                'ipAddress' => $request->ip(),
            ]);

            $result = AuthenticateUser::make()->authenticateWithToken($authData);

            session(['api_token' => $result['token']]);

            return redirect()->intended('/dashboard')->with('success', 'Login successful');
        } catch (UserException $e) {
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput($request->except('password'));
        } catch (\Exception $e) {
            \Log::error('Login controller error', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'email' => 'Authentication failed. Please try again.'
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Show the registration form.
     *
     * @return View The registration view
     */
    public function showRegisterForm(): View
    {
        return view('auth::register');
    }

    /**
     * Show the employee registration form.
     *
     * @return View The employee registration view
     */
    public function showEmployeeRegisterForm(): View
    {
        return view('auth::employee-register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request The registration request
     * @return RedirectResponse Redirect to login or back with errors
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            $userData = RegisterUserData::fromAuthRequest([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                'verification_code' => $request->verification_code,
            ]);

            RegisterUserAccount::run($userData);

            return redirect()->route('login')->with('success', 'Registration successful. Please login.');
        } catch (UserException $e) {
            return back()->withErrors([
                'email' => $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            \Log::error('Registration controller error', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'email' => 'Registration failed. Please try again.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request The request
     * @return RedirectResponse Redirect to login page
     */
    public function logout(Request $request): RedirectResponse
    {
        // Delete all user tokens
        if ($user = auth()->user()) {
            $user->tokens()->delete();
        }

        // Clear session
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
