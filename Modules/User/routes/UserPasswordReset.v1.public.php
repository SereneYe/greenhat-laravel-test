<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\UserResetPassword;

Route::post('/user/password/reset', UserResetPassword::class);
