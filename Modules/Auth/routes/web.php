<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Auth Module Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Auth module.
|
*/

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// Registration routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

// Password reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('forgot-password');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('auth.forgot-password');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('auth.reset-password');
