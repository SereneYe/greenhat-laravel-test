<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\AuthenticateUser;
use Modules\User\Actions\Auth\RegisterUserAccount;
use Modules\User\Actions\Auth\SendUserVerificationCode;
use Modules\User\Actions\Auth\ResetUserPasswordWithCode;

// 用户认证
Route::post('/user/auth/login', AuthenticateUser::class);
Route::post('/user/auth/register', RegisterUserAccount::class);

// 验证码
Route::post('/user/auth/send-verification-code', SendUserVerificationCode::class);

// 密码重置
Route::post('/user/auth/reset-password', ResetUserPasswordWithCode::class);
