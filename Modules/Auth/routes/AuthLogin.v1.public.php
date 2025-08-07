<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\AuthenticateUser;

Route::post('/api/v1/auth/login', AuthenticateUser::class)->name('api.v1.auth.login');
