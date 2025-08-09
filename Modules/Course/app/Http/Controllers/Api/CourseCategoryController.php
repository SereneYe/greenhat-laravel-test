<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
     * @param Request $request
     * @return Fractal
     */
    public function index(Request $request): Fractal
    {
        return (new GetCourseCategoryList())->asController($request);
    }

    /**
     * Store a newly created course category.
     *
     * @param Request $request
     * @return Fractal
     */
    public function store(Request $request): Fractal
    {
        return (new CreateCourseCategory())->asController($request);
    }

    /**
     * Display the specified course category by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Fractal
     */
    public function show(Request $request, int $id): Fractal
    {
        return (new GetCourseCategory())->asController($request, $id);
    }

    /**
     * Display the specified course category by slug.
     *
     * @param Request $request
     * @param string $slug
     * @return Fractal
     */
    public function showBySlug(Request $request, string $slug): Fractal
    {
        return (new GetCourseCategory())->asControllerSlug($request, $slug);
    }

    /**
     * Update the specified course category.
     *
     * @param Request $request
     * @param int $id
     * @return Fractal
     */
    public function update(Request $request, int $id): Fractal
    {
        return (new UpdateCourseCategory())->asController($request, $id);
    }

    /**
     * Remove the specified course category.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        return (new DeleteCourseCategory())->asController($request, $id);
    }
}
