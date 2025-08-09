<?php

namespace Modules\Course\Data\CourseEnrollment;

use Modules\Base\Traits\LaravelDataHelper;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Integer;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CourseEnrollmentFiltersData extends Data
{
    use LaravelDataHelper;

    public function __construct(
        #[In(['active', 'cancelled'])]
        public string|null|Optional $status,

        #[StringType]
        public string|null|Optional $date_from,

        #[StringType]
        public string|null|Optional $date_to,

        public array|Optional $category_ids,

        #[Integer, Min(1), Max(100)]
        public int|Optional $per_page,

        #[StringType, In(['enrolled_at', 'course_title', 'created_at'])]
        public string|Optional $sort_by,

        #[StringType, In(['asc', 'desc'])]
        public string|Optional $sort_direction,
    ) {}

    public static function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:active,cancelled'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:course_categories,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:enrolled_at,course_title,created_at'],
            'sort_direction' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the category IDs as an array, handling Optional type.
     *
     * @return array
     */
    public function getCategoryIds(): array
    {
        if ($this->category_ids instanceof Optional || is_null($this->category_ids)) {
            return [];
        }

        return is_array($this->category_ids) ? $this->category_ids : [];
    }
}
