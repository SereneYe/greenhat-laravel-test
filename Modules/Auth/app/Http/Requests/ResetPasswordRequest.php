<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone with a valid token can reset their password
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Reset token is required',
            'token.string' => 'Reset token must be a string',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'This email address is not registered',
            'password.required' => 'Password is required',
            'password.confirmed' => 'The password confirmation does not match',
        ];
    }
}
