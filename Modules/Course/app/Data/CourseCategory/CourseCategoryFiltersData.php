<?php

namespace Modules\Course\Data\CourseCategory;

use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CourseCategoryFiltersData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[StringType]
        public string|Optional $search,

        public bool|null|Optional $is_active,

        #[StringType]
        public string|Optional $sort_by = 'name',

        #[StringType]
        public string|Optional $sort_direction = 'asc',

        public int|Optional $per_page = 15,
    ) {}

    public static function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', 'in:name,created_at,courses_count'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
