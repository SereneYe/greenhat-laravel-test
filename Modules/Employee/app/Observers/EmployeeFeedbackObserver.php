<?php

namespace Modules\Employee\Observers;

use Modules\Employee\Actions\Feedback\NotifyAdminAboutEmployeeFeedback;
use Modules\Employee\Models\EmployeeFeedback;

class EmployeeFeedbackObserver
{
    public function created(EmployeeFeedback $model): void
    {
        NotifyAdminAboutEmployeeFeedback::dispatch($model);
    }
}
