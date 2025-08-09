<?php

namespace Modules\Course\Actions\Course;

use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\Course\CreateCourseData;
use Modules\Course\Exceptions\Course\CourseDuplicateException;
use Modules\Course\Exceptions\Course\CourseValidationException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseTransformer;
use Spatie\Fractal\Fractal;

class CreateCourse
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param CreateCourseData $data
     * @return Course
     * @throws CourseDuplicateException
     * @throws CourseValidationException
     */
    public function handle(CreateCourseData $data): Course
    {
        // Check if a course with the same title already exists
        if (Course::where('title', $data->title)->exists()) {
            throw CourseDuplicateException::duplicateTitle($data->title);
        }

        // Generate slug from title
        $slug = Str::slug($data->title);

        // Check if slug already exists and make it unique if needed
        $originalSlug = $slug;
        $counter = 1;

        while (Course::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        // Cover media validation removed

        // Validate categories if provided
        $categoryIds = [];
        if ($data->has('categories') && !empty($data->categories)) {
            $categoryIds = $data->categories;
            $existingCategoryIds = CourseCategory::whereIn('id', $categoryIds)->pluck('id')->toArray();
            $invalidCategoryIds = array_diff($categoryIds, $existingCategoryIds);

            if (!empty($invalidCategoryIds)) {
                throw CourseValidationException::invalidCategories($invalidCategoryIds);
            }
        }

        // Create the course
        $course = Course::create([
            'title' => $data->title,
            'slug' => $slug,
            'description' => $data->has('description') ? $data->description : null,
            'content' => $data->has('content') ? $data->content : null,
            'instructor' => $data->has('instructor') ? $data->instructor : null,
            'price' => $data->price,
            'level' => $data->level,
            'duration_hours' => $data->duration_hours,
        ]);

        // Attach categories if provided
        if (!empty($categoryIds)) {
            $course->categories()->attach($categoryIds);
        }

        return $course;
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @return Course
     */
    public function asController(ActionRequest $request): Course
    {
        $data = CreateCourseData::validateAndCreate($request->all());
        return $this->handle($data);
    }

    /**
     * Format the response.
     *
     * @param Course $course
     * @return Fractal
     */
    public function jsonResponse(Course $course): Fractal
    {
        return fractal($course, new CourseTransformer())
            ->includeCategories();
    }
}
