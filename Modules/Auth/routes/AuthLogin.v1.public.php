<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Actions\Auth\LoginUser;

Route::post('/api/v1/auth/login', LoginUser::class);
