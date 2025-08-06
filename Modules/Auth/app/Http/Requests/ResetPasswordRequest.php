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
            'token.required' => '重置令牌是必填项',
            'token.string' => '重置令牌必须是字符串',
            'email.required' => '邮箱地址是必填项',
            'email.email' => '请输入有效的邮箱地址',
            'email.exists' => '该邮箱地址未注册',
            'password.required' => '密码是必填项',
            'password.confirmed' => '两次输入的密码不一致',
        ];
    }
}
