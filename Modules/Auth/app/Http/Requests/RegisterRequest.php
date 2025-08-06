<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can register
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // User basic information validation rules
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Employee information validation rules
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => '邮箱地址是必填项',
            'email.email' => '请输入有效的邮箱地址',
            'email.unique' => '该邮箱地址已被注册',
            'password.required' => '密码是必填项',
            'password.confirmed' => '两次输入的密码不一致',
            'first_name.required' => '名字是必填项',
            'first_name.string' => '名字必须是字符串',
            'first_name.max' => '名字不能超过255个字符',
            'last_name.required' => '姓氏是必填项',
            'last_name.string' => '姓氏必须是字符串',
            'last_name.max' => '姓氏不能超过255个字符',
            'phone.string' => '电话号码必须是字符串',
            'phone.max' => '电话号码不能超过20个字符',
            'position.string' => '职位必须是字符串',
            'position.max' => '职位不能超过255个字符',
            'department.string' => '部门必须是字符串',
            'department.max' => '部门不能超过255个字符',
            'hire_date.date' => '入职日期必须是有效的日期',
            'salary.numeric' => '薪资必须是数字',
            'salary.min' => '薪资不能为负数',
        ];
    }
}
