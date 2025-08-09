<?php

namespace Modules\Course\Data\Course;

use Illuminate\Validation\Rule;
use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Array as ArrayAttribute;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateCourseData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[StringType, Max(255)]
        public string|Optional $title,

        #[StringType, Max(1000)]
        public string|null|Optional $description,

        #[StringType]
        public string|null|Optional $content,

        #[StringType, Max(255)]
        public string|null|Optional $instructor,

        #[Numeric, Min(0)]
        public float|Optional $price,

        #[StringType, In(['beginner', 'intermediate', 'advanced', 'expert'])]
        public string|Optional $level,

        #[Numeric, Min(1)]
        public int|Optional $duration_hours,

        #[ArrayAttribute, Exists('course_categories', 'id')]
        public array|Optional $categories,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'level' => ['sometimes', 'string', 'in:beginner,intermediate,advanced,expert'],
            'duration_hours' => ['sometimes', 'integer', 'min:1'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['exists:course_categories,id'],
        ];
    }

    public static function validateAndCreateWithId(array $data, int $courseId): static
    {
        $rules = static::rules();

        // Add unique rule for title with the course ID
        if (isset($data['title'])) {
            $rules['title'][] = Rule::unique('courses', 'title')->ignore($courseId);
        }

        $validator = validator()->make($data, $rules);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return static::from($data);
    }
}
