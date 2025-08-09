<?php

namespace Modules\Course\Actions\CourseCategory;

use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\CourseCategory\UpdateCourseCategoryData;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryDuplicateException;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryNotFoundException;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Spatie\Fractal\Fractal;

class UpdateCourseCategory
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param UpdateCourseCategoryData $data
     * @return CourseCategory
     * @throws CourseCategoryDuplicateException
     */
    public function handle(UpdateCourseCategoryData $data): CourseCategory
    {
        $category = $data->courseCategory;
        $updates = [];

        // Update name if provided
        if ($data->has('name') && $data->name !== $category->name) {
            // Check if the new name already exists for another category
            if (CourseCategory::where('name', $data->name)
                ->where('id', '!=', $category->id)
                ->exists()) {
                throw CourseCategoryDuplicateException::duplicateName($data->name);
            }

            $updates['name'] = $data->name;

            // Generate new slug if name changes
            $slug = Str::slug($data->name);

            // Check if slug already exists and make it unique if needed
            $originalSlug = $slug;
            $counter = 1;

            while (CourseCategory::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $updates['slug'] = $slug;
        }

        // Update description if provided
        if ($data->has('description')) {
            $updates['description'] = $data->description;
        }

        // Update color if provided
        if ($data->has('color')) {
            $updates['color'] = $data->color;
        }

        // Update is_active if provided
        if ($data->has('is_active')) {
            $updates['is_active'] = $data->is_active;
        }

        // Update the category if there are changes
        if (!empty($updates)) {
            $category->update($updates);
        }

        return $category->fresh();
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return CourseCategory
     * @throws CourseCategoryNotFoundException
     */
    public function asController(ActionRequest $request, int $id): CourseCategory
    {
        $category = CourseCategory::find($id);

        if (!$category) {
            throw CourseCategoryNotFoundException::forId($id);
        }

        return $this->handle(
            UpdateCourseCategoryData::validateAndCreate([
                ...$request->all(),
                'courseCategory' => $category,
            ])
        );
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
