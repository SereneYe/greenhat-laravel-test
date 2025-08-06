<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Actions\Auth\RegisterUser;

Route::post('/api/v1/auth/register', RegisterUser::class);
