<?php

namespace Modules\Course\Actions\Course;

use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\Course\UpdateCourseData;
use Modules\Course\Exceptions\Course\CourseDuplicateException;
use Modules\Course\Exceptions\Course\CourseNotFoundException;
use Modules\Course\Exceptions\Course\CourseValidationException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseTransformer;
use Spatie\Fractal\Fractal;

class UpdateCourse
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param int $id
     * @param UpdateCourseData $data
     * @return Course
     * @throws CourseNotFoundException
     * @throws CourseDuplicateException
     * @throws CourseValidationException
     */
    public function handle(int $id, UpdateCourseData $data): Course
    {
        // Find the course or throw an exception
        $course = Course::find($id);
        if (!$course) {
            throw CourseNotFoundException::forId($id);
        }

        // Check for duplicate title if title is being updated
        if ($data->has('title') && $data->title !== $course->title) {
            if (Course::where('title', $data->title)->exists()) {
                throw CourseDuplicateException::duplicateTitle($data->title);
            }

            // Generate new slug from title
            $slug = Str::slug($data->title);

            // Check if slug already exists and make it unique if needed
            $originalSlug = $slug;
            $counter = 1;

            while (Course::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            // Update the slug
            $course->slug = $slug;
        }

        // Cover media validation removed

        // Validate categories if provided
        if ($data->has('categories')) {
            $categoryIds = $data->categories;
            if (!empty($categoryIds)) {
                $existingCategoryIds = CourseCategory::whereIn('id', $categoryIds)->pluck('id')->toArray();
                $invalidCategoryIds = array_diff($categoryIds, $existingCategoryIds);

                if (!empty($invalidCategoryIds)) {
                    throw CourseValidationException::invalidCategories($invalidCategoryIds);
                }
            }
        }

        // Update the course fields
        if ($data->has('title')) {
            $course->title = $data->title;
        }

        if ($data->has('description')) {
            $course->description = $data->description;
        }

        if ($data->has('content')) {
            $course->content = $data->content;
        }

        if ($data->has('instructor')) {
            $course->instructor = $data->instructor;
        }

        if ($data->has('price')) {
            $course->price = $data->price;
        }

        if ($data->has('level')) {
            $course->level = $data->level;
        }

        if ($data->has('duration_hours')) {
            $course->duration_hours = $data->duration_hours;
        }

        // Cover media update removed

        // Save the updated course
        $course->save();

        // Update categories if provided
        if ($data->has('categories')) {
            $course->categories()->sync($data->categories);
        }

        return $course;
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return Course
     */
    public function asController(ActionRequest $request, int $id): Course
    {
        $data = UpdateCourseData::validateAndCreateWithId($request->all(), $id);
        return $this->handle($id, $data);
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
