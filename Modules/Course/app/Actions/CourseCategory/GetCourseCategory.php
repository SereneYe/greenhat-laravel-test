<?php

namespace Modules\Course\Actions\CourseCategory;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryNotFoundException;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Spatie\Fractal\Fractal;

class GetCourseCategory
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param int|string $identifier ID or slug of the course category
     * @param bool $useSlug Whether to use the identifier as a slug
     * @return CourseCategory
     * @throws CourseCategoryNotFoundException
     */
    public function handle($identifier, bool $useSlug = false): CourseCategory
    {
        $query = CourseCategory::query();

        if ($useSlug) {
            $category = $query->where('slug', $identifier)->first();

            if (!$category) {
                throw CourseCategoryNotFoundException::forSlug($identifier);
            }
        } else {
            $category = $query->find($identifier);

            if (!$category) {
                throw CourseCategoryNotFoundException::forId($identifier);
            }
        }

        return $category;
    }

    /**
     * Handle the action as a controller for ID-based lookup.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return Fractal
     * @throws CourseCategoryNotFoundException
     */
    public function asController(ActionRequest $request, int $id): Fractal
    {
        $category = $this->handle($id);

        // Return Fractal format response
        return fractal($category, new CourseCategoryTransformer())
            ->serializeWith(new \League\Fractal\Serializer\DataArraySerializer());
    }

    /**
     * Handle the action as a controller for slug-based lookup.
     *
     * @param ActionRequest $request
     * @param string $slug
     * @return Fractal
     * @throws CourseCategoryNotFoundException
     */
    public function asControllerSlug(ActionRequest $request, string $slug): Fractal
    {
        $category = $this->handle($slug, true);

        // Return Fractal format response
        return fractal($category, new CourseCategoryTransformer())
            ->serializeWith(new \League\Fractal\Serializer\DataArraySerializer());
    }

    /**
     * Format the response.
     *
     * @param CourseCategory $category
     * @return Fractal
     */
    public function jsonResponse(CourseCategory $category): Fractal
    {
        return fractal($category, new CourseCategoryTransformer());
    }
}
