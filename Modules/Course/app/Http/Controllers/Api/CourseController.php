<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Fractal\Serializer\DataArraySerializer;
use Lorisleiva\Actions\ActionRequest;
use Modules\Course\Actions\Course\CreateCourse;
use Modules\Course\Actions\Course\DeleteCourse;
use Modules\Course\Actions\Course\GetCourse;
use Modules\Course\Actions\Course\GetCourseList;
use Modules\Course\Actions\Course\UpdateCourse;
use Modules\Course\Data\Course\CourseFiltersData;
use Modules\Course\Transformers\CourseTransformer;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        $filters = CourseFiltersData::from($request->all());
        $courses = (new GetCourseList())->handle($filters);

        return fractal($courses, new CourseTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Store a newly created course.
     *
     * @param ActionRequest $request
     * @return array
     */
    public function store(ActionRequest $request): array
    {
        $course = (new CreateCourse())->asController($request);
        return fractal($course, new CourseTransformer())
            ->includeCategories()
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Display the specified course by ID.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return array
     */
    public function show(ActionRequest $request, int $id): array
    {
        $course = (new GetCourse())->handle($id);
        return fractal($course, new CourseTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Display the specified course by slug.
     *
     * @param ActionRequest $request
     * @param string $slug
     * @return array
     */
    public function showBySlug(ActionRequest $request, string $slug): array
    {
        $course = (new GetCourse())->handleBySlug($slug);
        return fractal($course, new CourseTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Update the specified course.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return array
     */
    public function update(ActionRequest $request, int $id): array
    {
        $course = (new UpdateCourse())->asController($request, $id);
        return fractal($course, new CourseTransformer())
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Remove the specified course.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(ActionRequest $request, int $id): JsonResponse
    {
        return (new DeleteCourse())->asController($request, $id);
    }
}
