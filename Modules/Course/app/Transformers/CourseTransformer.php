<?php

namespace Modules\Course\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Modules\Course\Models\Course;
use Modules\Media\Transformers\FilamentMediaLibraryTransformer;

class CourseTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'categories',
        'coverMedia',
    ];

    /**
     * Transform the Course model.
     *
     * @param Course $model
     * @return array
     */
    public function transform($model): array
    {
        $data = $model instanceof Course ? $model : new Course;

        return [
            'id' => $data->getKey(),
            'title' => $data->title,
            'slug' => $data->slug,
            'description' => $data->description,
            'content' => $data->content,
            'instructor' => $data->instructor,
            'price' => (float) $data->price,
            'level' => $data->level,
            'durationHours' => (int) $data->duration_hours,
            'coverMediaId' => null,
            'enrollmentCount' => $data->getEnrollmentCount(),
            'categoriesCount' => $data->categories()->count(),
            'hasCoverMedia' => false,
            'createdAt' => $data->created_at?->toIso8601String(),
            'updatedAt' => $data->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Include categories in the transformation.
     *
     * @param Course $model
     * @return Collection
     */
    public function includeCategories(Course $model): Collection
    {
        return $this->collection($model->categories, new CourseCategoryTransformer);
    }

    /**
     * Include cover media in the transformation.
     * Always returns null as cover media functionality has been removed.
     *
     * @param Course $model
     * @return Item|null
     */
    public function includeCoverMedia(Course $model): ?Item
    {
        return null;
    }
}
