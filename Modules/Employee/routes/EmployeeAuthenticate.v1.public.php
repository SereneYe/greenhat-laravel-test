<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Actions\Auth\AuthenticateEmployee;

Route::post('/employee/authenticate', AuthenticateEmployee::class);
