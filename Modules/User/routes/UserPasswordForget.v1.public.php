<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\SendUserResetPasswordLink;

Route::post('/user/password/forget', SendUserResetPasswordLink::class);
