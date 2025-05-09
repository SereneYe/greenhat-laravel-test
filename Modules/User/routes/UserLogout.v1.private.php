<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Actions\Auth\LogOutUser;

Route::post('/users/logout', LogOutUser::class);
