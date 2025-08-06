<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Auth\Actions\Auth\LoginUser;
use Modules\Auth\Actions\Auth\RegisterUser;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth::login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            // Call the LoginUser action
            $response = LoginUser::run($request);

            // If we get here, login was successful
            return redirect()->intended('/')->with('success', 'Login successful');
        } catch (\Exception $e) {
            // If there was an error, redirect back with error message
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth::register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            // Call the RegisterUser action
            $response = RegisterUser::run($request);

            // If we get here, registration was successful
            return redirect()->route('login')->with('success', 'Registration successful. Please login.');
        } catch (\Exception $e) {
            // If there was an error, redirect back with error message
            return back()->withErrors([
                'email' => 'There was an error registering your account. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
