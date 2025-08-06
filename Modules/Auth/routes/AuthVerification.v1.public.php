<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Actions\Auth\SendVerificationCode;

/*
|--------------------------------------------------------------------------
| Auth Verification API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for verification functionality.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::post('/auth/send-verification', SendVerificationCode::class)
    ->name('auth.send-verification');
