<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\RegisterUserAccount;

Route::post('/api/v1/auth/register', RegisterUserAccount::class)->name('api.v1.auth.register');
