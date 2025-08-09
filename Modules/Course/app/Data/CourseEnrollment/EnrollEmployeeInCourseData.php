<?php

namespace Modules\Course\Data\CourseEnrollment;

use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EnrollEmployeeInCourseData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[Required, Exists('courses', 'id')]
        public int $course_id,

        #[StringType, Max(500)]
        public string|null|Optional $notes,
    ) {}

    public static function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
