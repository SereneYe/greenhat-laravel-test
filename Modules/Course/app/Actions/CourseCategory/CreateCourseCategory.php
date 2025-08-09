<?php

namespace Modules\Course\Actions\CourseCategory;

use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\CourseCategory\CreateCourseCategoryData;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryDuplicateException;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Spatie\Fractal\Fractal;

class CreateCourseCategory
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param CreateCourseCategoryData $data
     * @return CourseCategory
     * @throws CourseCategoryDuplicateException
     */
    public function handle(CreateCourseCategoryData $data): CourseCategory
    {
        // Check if a category with the same name already exists
        if (CourseCategory::where('name', $data->name)->exists()) {
            throw CourseCategoryDuplicateException::duplicateName($data->name);
        }

        // Generate slug from name
        $slug = Str::slug($data->name);

        // Check if slug already exists and make it unique if needed
        $originalSlug = $slug;
        $counter = 1;

        while (CourseCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Create the category
        return CourseCategory::create([
            'name' => $data->name,
            'slug' => $slug,
            'description' => $data->has('description') ? $data->description : null,
            'color' => $data->has('color') ? $data->color : null,
            'is_active' => $data->has('is_active') ? $data->is_active : true,
        ]);
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @return CourseCategory
     */
    public function asController(ActionRequest $request): CourseCategory
    {
        return $this->handle(
            CreateCourseCategoryData::validateAndCreate($request->all())
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
