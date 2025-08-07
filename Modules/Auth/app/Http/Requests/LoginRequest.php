<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Modules\Auth\Exceptions\AuthException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always returns true as anyone can attempt to login
     */
    public function authorize(): bool
    {
        return true; // Anyone can attempt to login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>> The validation rules
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> The custom error messages
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.string' => 'Email address must be a string',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'remember.boolean' => 'Remember me option must be a boolean value',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Modules\Auth\Exceptions\AuthException When credentials are invalid
     * @return void
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(
            $this->only('email', 'password'),
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey());

            throw AuthException::invalidCredentials();
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Modules\Auth\Exceptions\AuthException When too many login attempts
     * @return void
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw AuthException::tooManyLoginAttempts($seconds);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string The throttle key
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
