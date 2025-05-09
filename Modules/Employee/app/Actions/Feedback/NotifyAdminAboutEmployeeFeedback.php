<?php

namespace Modules\Employee\Actions\Feedback;

use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Emails\EmployeeFeedbackEmail;
use Modules\Employee\Models\EmployeeFeedback;

class NotifyAdminAboutEmployeeFeedback
{
    use AsAction;

    public function handle(EmployeeFeedback $feedback): void
    {
        Mail::send(new EmployeeFeedbackEmail($feedback));
    }
}
