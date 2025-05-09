<?php

use Modules\Employee\Actions\Feedback\CreateEmployeeFeedback;

Route::post('/employee-feedback', CreateEmployeeFeedback::class);
