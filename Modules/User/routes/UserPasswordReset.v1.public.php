<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\ResetUserPasswordWithCode;

Route::post('/v1/user/password/reset', ResetUserPasswordWithCode::class)->name('api.v1.user.password.reset');
