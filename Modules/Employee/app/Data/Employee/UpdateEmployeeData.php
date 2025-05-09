<?php

namespace Modules\Employee\Data\Employee;

use Modules\Base\Traits\LaravelDataHelper;
use Modules\Employee\Models\Employee;
use Modules\User\Data\User\UpdateUserData;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateEmployeeData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        public Employee $employee,
        public UpdateUserData|Optional $userData,
    ) {}
}
