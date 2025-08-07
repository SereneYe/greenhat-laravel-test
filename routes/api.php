<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Version 1 API Routes
Route::prefix('v1')->group(function () {

    // Employee Registration (Public)
    Route::post('/employee-registration', [\Modules\Employee\Actions\Registration\RegisterEmployee::class, 'asController'])
        ->name('employee.registration.api');

    // User Authentication & Management
    Route::prefix('user')->group(function () {
        Route::post('/auth/login', [\Modules\User\Actions\Auth\AuthenticateUser::class, 'asController'])
            ->name('user.login');
        Route::post('/auth/register', [\Modules\User\Actions\Auth\RegisterUserAccount::class, 'asController'])
            ->name('user.register');
        Route::post('/password/reset', [\Modules\User\Actions\Auth\ResetUserPasswordWithCode::class, 'asController'])
            ->name('user.password.reset');
    });

    // Employee Authentication
    Route::prefix('employee')->group(function () {
        Route::post('/auth/login', [\Modules\Employee\Actions\Auth\AuthenticateEmployee::class, 'asController'])
            ->name('employee.login');
    });

    // Verification & Password Reset
    Route::prefix('auth')->group(function () {

        Route::post('/send-verification-code', [\Modules\User\Actions\Auth\SendUserVerificationCode::class, 'asController'])
            ->name('auth.send-verification-code');
    });
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
})->name('api.health');
