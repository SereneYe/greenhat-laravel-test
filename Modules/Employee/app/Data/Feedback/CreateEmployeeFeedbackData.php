<?php

namespace Modules\Employee\Data\Feedback;

use Modules\Base\Traits\LaravelDataHelper;
use Modules\Employee\Models\Employee;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateEmployeeFeedbackData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        public Employee $employee,
        public string $comment,
        public string|null|Optional $name,
        public string|null|Optional $email,
    ) {}
}
