<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PasswordResetController;
use Modules\Media\Http\Controllers\MediaController;

/*
|--------------------------------------------------------------------------
| Auth Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Auth module.
|
*/

// Public routes
Route::group(['middleware' => 'guest'], function () {
    // Login routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Registration routes
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // Employee Registration route
    Route::get('/employee/register', [AuthController::class, 'showEmployeeRegisterForm'])->name('employee.register');

    // Password reset routes
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('forgot-password');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('auth.forgot-password');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('auth.reset-password');
});

// Protected routes
Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('auth::dashboard'); // Need to create dashboard view
    })->name('dashboard');
});

// Media routes
Route::group(['middleware' => 'web'], function () {
    Route::resource('media', MediaController::class)->names('media');
});

// Debug routes - only available in local and development environments
if (app()->environment(['local', 'development'])) {
    Route::get('/debug/verification/{email}/{purpose?}', function($email, $purpose = 'registration') {
        $cacheKey = 'verification_code_' . $purpose . '_' . md5($email);
        $cachedCode = Cache::get($cacheKey);

        return response()->json([
            'email' => $email,
            'purpose' => $purpose,
            'email_md5' => md5($email),
            'cache_key' => $cacheKey,
            'cached_code' => $cachedCode,
            'cached_code_type' => gettype($cachedCode),
            'cache_exists' => Cache::has($cacheKey),
            'cache_ttl' => Cache::getStore() instanceof \Illuminate\Cache\MemcachedStore ? 'N/A' : 'Check logs',
        ]);
    })->name('debug.verification');
}
