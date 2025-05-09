<?php

namespace Modules\Employee\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Models\EmployeeFeedback;

class EmployeeFeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmployeeFeedback $feedback,
    ) {}

    public function build(): self
    {
        return $this
            ->to('admin@greenhat.net', 'Greenhat')
            ->view('employee::emails.feedback')
            ->subject('[Greenhat] - Employee feedback submission')
            ->with([
                'submissionDate' => $this->feedback->createdAt?->toDateTimeString(),
                'employeeId' => $this->feedback->employeeId,
                'feedbackName' => $this->feedback->name,
                'feedbackEmail' => $this->feedback->email,
                'feedbackComment' => $this->feedback->comment
            ]);
    }
}
