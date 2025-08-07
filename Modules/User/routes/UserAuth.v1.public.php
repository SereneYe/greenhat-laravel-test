<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\AuthenticateUser;
use Modules\User\Actions\Auth\RegisterUserAccount;
use Modules\User\Actions\Auth\SendUserVerificationCode;
use Modules\User\Actions\Auth\ResetUserPasswordWithCode;

// user authentication
Route::post('/user/auth/login', AuthenticateUser::class);
Route::post('/user/auth/register', RegisterUserAccount::class);

// verification code
Route::post('/user/auth/send-verification-code', SendUserVerificationCode::class);

// reset password
Route::post('/user/auth/reset-password', ResetUserPasswordWithCode::class);
