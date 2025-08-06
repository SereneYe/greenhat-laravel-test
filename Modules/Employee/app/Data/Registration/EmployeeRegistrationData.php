<?php

namespace Modules\Employee\Data\Registration;

use Modules\Employee\Models\Employee;
use Modules\User\Models\User;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class EmployeeRegistrationData extends Data
{
    public function __construct(
        #[Required]
        public string $firstName,

        #[Required]
        public string $lastName,

        #[Required, Unique(User::class, 'email'), Email]
        public string $email,

        #[Required, In(['ACME'])]
        public string $registrationCode,

        #[Required, In(Employee::class, 'getRoleOptions')]
        public string $role,

        #[StringType, Max(255)]
        public ?string $highestQualification = null,

        #[Numeric]
        public ?float $desiredSalary = null,

        #[StringType]
        public ?string $note = null
    ) {}
}
