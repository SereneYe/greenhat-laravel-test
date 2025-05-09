<?php

use Modules\Employee\Actions\Auth\ReadEmployeeAuthUser;

Route::get('/employee/user', ReadEmployeeAuthUser::class);
