<?php

namespace Modules\Course\Actions\CourseCategory;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Exceptions\CourseCategory\CourseCategoryNotFoundException;
use Modules\Course\Models\CourseCategory;
use Illuminate\Http\JsonResponse;

class DeleteCourseCategory
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param CourseCategory $category
     * @return bool
     */
    public function handle(CourseCategory $category): bool
    {
        return $category->delete();
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return bool
     * @throws CourseCategoryNotFoundException
     */
    public function asController(ActionRequest $request, int $id): bool
    {
        $category = CourseCategory::find($id);

        if (!$category) {
            throw CourseCategoryNotFoundException::forId($id);
        }

        return $this->handle($category);
    }

    /**
     * Format the response.
     *
     * @param bool $result
     * @return JsonResponse
     */
    public function jsonResponse(bool $result): JsonResponse
    {
        if ($result) {
            return response()->json([
                'message' => 'Course category deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete course category',
        ], 500);
    }
}
