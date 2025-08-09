<?php

namespace Modules\Course\Actions\Course;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Exceptions\Course\CourseNotFoundException;
use Modules\Course\Models\Course;
use Modules\Course\Transformers\CourseTransformer;
use Spatie\Fractal\Fractal;

class GetCourse
{
    use AsAction;

    /**
     * Handle the action to get a course by ID.
     *
     * @param int $id
     * @param array $with Relationships to eager load
     * @param bool $withTrashed Whether to include soft deleted courses
     * @return Course
     * @throws CourseNotFoundException
     */
    public function handle(int $id, array $with = [], bool $withTrashed = false): Course
    {
        $query = Course::query();

        // Include soft deleted courses if requested
        if ($withTrashed) {
            $query->withTrashed();
        }

        // Eager load relationships if specified
        if (!empty($with)) {
            $query->with($with);
        }

        $course = $query->find($id);

        if (!$course) {
            throw CourseNotFoundException::forId($id);
        }

        return $course;
    }

    /**
     * Handle the action to get a course by slug.
     *
     * @param string $slug
     * @param array $with Relationships to eager load
     * @param bool $withTrashed Whether to include soft deleted courses
     * @return Course
     * @throws CourseNotFoundException
     */
    public function handleBySlug(string $slug, array $with = [], bool $withTrashed = false): Course
    {
        $query = Course::query();

        // Include soft deleted courses if requested
        if ($withTrashed) {
            $query->withTrashed();
        }

        // Eager load relationships if specified
        if (!empty($with)) {
            $query->with($with);
        }

        $course = $query->where('slug', $slug)->first();

        if (!$course) {
            throw CourseNotFoundException::forSlug($slug);
        }

        return $course;
    }

    /**
     * Handle the action as a controller for ID lookup.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return Course
     */
    public function asController(ActionRequest $request, int $id): Course
    {
        $with = $request->input('with', []);
        $withTrashed = $request->boolean('with_trashed', false);

        return $this->handle($id, $with, $withTrashed);
    }

    /**
     * Handle the action as a controller for slug lookup.
     *
     * @param ActionRequest $request
     * @param string $slug
     * @return Course
     */
    public function asControllerSlug(ActionRequest $request, string $slug): Course
    {
        $with = $request->input('with', []);
        $withTrashed = $request->boolean('with_trashed', false);

        return $this->handleBySlug($slug, $with, $withTrashed);
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
