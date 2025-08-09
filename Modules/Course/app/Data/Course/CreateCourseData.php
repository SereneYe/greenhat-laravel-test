<?php

namespace Modules\Course\Data\Course;

use Illuminate\Validation\Rule;
use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Array as ArrayAttribute;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateCourseData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[Required, StringType, Max(255)]
        public string $title,

        #[StringType, Max(1000)]
        public string|null|Optional $description,

        #[StringType]
        public string|null|Optional $content,

        #[StringType, Max(255)]
        public string|null|Optional $instructor,

        #[Required, Numeric, Min(0)]
        public float $price,

        #[Required, StringType, In(['beginner', 'intermediate', 'advanced', 'expert'])]
        public string $level,

        #[Required, Numeric, Min(1)]
        public int $duration_hours,

        #[ArrayAttribute, Exists('course_categories', 'id')]
        public array|Optional $categories,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('courses', 'title')],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'level' => ['required', 'string', 'in:beginner,intermediate,advanced,expert'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:course_categories,id'],
        ];
    }
}
