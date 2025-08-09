<?php

namespace Modules\Course\Data\CourseCategory;

use Illuminate\Validation\Rule;
use Modules\Base\Traits\LaravelDataHelper;
use Modules\Course\Models\CourseCategory;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateCourseCategoryData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        public CourseCategory $courseCategory,

        #[StringType, Max(255)]
        public string|Optional $name,

        #[StringType, Max(1000)]
        public string|null|Optional $description,

        #[StringType, Regex('/^#[0-9A-Fa-f]{6}$/')]
        public string|null|Optional $color,

        public bool|Optional $is_active,
    ) {}

    public static function rules(CourseCategory $courseCategory): array
    {
        return [
            'name' => [
                'string',
                'max:255',
                Rule::unique('course_categories', 'name')->ignore($courseCategory->id)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
        ];
    }
}
