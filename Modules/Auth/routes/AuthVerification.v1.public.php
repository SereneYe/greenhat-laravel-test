<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\SendUserVerificationCode;

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

Route::post('/auth/send-verification', SendUserVerificationCode::class)
    ->name('api.v1.auth.send-verification');
