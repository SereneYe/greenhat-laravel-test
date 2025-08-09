<?php

namespace Modules\Course\Data\CourseCategory;

use Illuminate\Validation\Rule;
use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateCourseCategoryData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,

        #[StringType, Max(1000)]
        public string|null|Optional $description,

        #[StringType, Regex('/^#[0-9A-Fa-f]{6}$/')]
        public string|null|Optional $color,

        public bool|Optional $is_active = true,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('course_categories', 'name')],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
        ];
    }
}
