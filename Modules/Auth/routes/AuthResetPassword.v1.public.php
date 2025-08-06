<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Actions\Auth\ResetPassword;

Route::post('/auth/reset-password', ResetPassword::class);
