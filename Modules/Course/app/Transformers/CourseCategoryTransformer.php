<?php

namespace Modules\Course\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Modules\Course\Models\CourseCategory;

class CourseCategoryTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'courses',
    ];

    /**
     * Transform the CourseCategory model.
     *
     * @param CourseCategory $model
     * @return array
     */
    public function transform($model): array
    {
        $data = $model instanceof CourseCategory ? $model : new CourseCategory;

        return [
            'id' => $data->getKey(),
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'color' => $data->color,
            'isActive' => (bool) $data->is_active,
            'coursesCount' => $data->getCoursesCount(),
            'activeCoursesCount' => $data->getActiveCoursesCount(),
            'createdAt' => $data->created_at?->toIso8601String(),
            'updatedAt' => $data->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Include courses in the transformation.
     *
     * @param CourseCategory $model
     * @return Collection
     */
    public function includeCourses(CourseCategory $model): Collection
    {
        return $this->collection($model->courses, new CourseTransformer);
    }
}
