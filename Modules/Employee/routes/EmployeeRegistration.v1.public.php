<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Actions\Registration\RegisterEmployee;

Route::post('employee-registration', RegisterEmployee::class)->name('employee.registration.api');
