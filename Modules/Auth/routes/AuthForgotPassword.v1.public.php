<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Actions\Auth\ForgotPassword;

Route::post('/api/v1/auth/forgot-password', ForgotPassword::class);
