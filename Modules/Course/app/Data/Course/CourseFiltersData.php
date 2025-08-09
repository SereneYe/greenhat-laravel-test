<?php

namespace Modules\Course\Data\Course;

use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Array as ArrayAttribute;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Attributes\WithoutValidation;

class CourseFiltersData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[StringType]
        public ?string $search = null,

        #[StringType, In(['beginner', 'intermediate', 'advanced', 'expert'])]
        public ?string $level = null,

        #[ArrayAttribute, Exists('course_categories', 'id')]
        public ?array $categories = null,

        public ?float $min_price = null,

        public ?float $max_price = null,

        public ?int $min_duration = null,

        public ?int $max_duration = null,

        #[StringType, In(['title', 'price', 'created_at', 'duration_hours'])]
        public ?string $sort_by = null,

        #[StringType, In(['asc', 'desc'])]
        public ?string $sort_direction = null,

        #[StringType, In(['10', '25', '50', '100'])]
        public ?string $per_page = null,

        #[WithoutValidation]
        public ?array $with = null,
    ) {}

    public static function rules(): array
    {
        return [
            'search' => ['sometimes', 'string'],
            'level' => ['sometimes', 'string', 'in:beginner,intermediate,advanced,expert'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:course_categories,id'],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0'],
            'min_duration' => ['sometimes', 'integer', 'min:0'],
            'max_duration' => ['sometimes', 'integer', 'min:0'],
            'sort_by' => ['sometimes', 'string', 'in:title,price,created_at,duration_hours'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'string', 'in:10,25,50,100'],
        ];
    }

    public function getPerPage(): int
    {
        return $this->has('per_page') && $this->per_page !== null ? (int) $this->per_page : 10;
    }

    public function getSortBy(): string
    {
        return $this->has('sort_by') && $this->sort_by !== null ? $this->sort_by : 'created_at';
    }

    public function getSortDirection(): string
    {
        return $this->has('sort_direction') && $this->sort_direction !== null ? $this->sort_direction : 'desc';
    }
}
