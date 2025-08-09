<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Lorisleiva\Actions\ActionRequest;
use Modules\Course\Actions\CourseCategory\CreateCourseCategory;
use Modules\Course\Actions\CourseCategory\DeleteCourseCategory;
use Modules\Course\Actions\CourseCategory\GetCourseCategory;
use Modules\Course\Actions\CourseCategory\GetCourseCategoryList;
use Modules\Course\Actions\CourseCategory\UpdateCourseCategory;
use Spatie\Fractal\Fractal;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of course categories.
     *
     * @param ActionRequest $request
     * @return array
     */
    public function index(ActionRequest $request): array
    {
        $fractal = (new GetCourseCategoryList())->asController($request);
        return $fractal->toArray();
    }

    /**
     * Store a newly created course category.
     *
     * @param ActionRequest $request
     * @return array
     */
    public function store(ActionRequest $request): array
    {
        $fractal = (new CreateCourseCategory())->asController($request);
        return $fractal->toArray();
    }

    /**
     * Display the specified course category by ID.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return array
     */
    public function show(ActionRequest $request, int $id): array
    {
        $fractal = (new GetCourseCategory())->asController($request, $id);
        return $fractal->toArray();
    }

    /**
     * Display the specified course category by slug.
     *
     * @param ActionRequest $request
     * @param string $slug
     * @return array
     */
    public function showBySlug(ActionRequest $request, string $slug): array
    {
        $fractal = (new GetCourseCategory())->asControllerSlug($request, $slug);
        return $fractal->toArray();
    }

    /**
     * Update the specified course category.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return array
     */
    public function update(ActionRequest $request, int $id): array
    {
        $fractal = (new UpdateCourseCategory())->asController($request, $id);
        return $fractal->toArray();
    }

    /**
     * Remove the specified course category.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(ActionRequest $request, int $id): JsonResponse
    {
        return (new DeleteCourseCategory())->asController($request, $id);
    }
}
