<?php

namespace Modules\Course\Actions\Course;

use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Exceptions\Course\CourseNotFoundException;
use Modules\Course\Models\Course;

class DeleteCourse
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param int $id
     * @param bool $forceDelete
     * @return bool
     * @throws CourseNotFoundException
     */
    public function handle(int $id, bool $forceDelete = false): bool
    {
        // Find the course or return false if it's already deleted
        $course = Course::withTrashed()->find($id);
        if (!$course) {
            return false;
        }

        // If the course is already deleted, return false
        if (!$forceDelete && $course->trashed()) {
            return false;
        }

        // Delete the course (soft delete or force delete)
        if ($forceDelete) {
            // Detach all categories before force deleting
            $course->categories()->detach();
            return $course->forceDelete();
        } else {
            return $course->delete();
        }
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function asController(ActionRequest $request, int $id): JsonResponse
    {
        $this->handle($id);

        return response()->json([
            'message' => 'Course deleted successfully'
        ], 200);
    }

    /**
     * Format the response.
     *
     * @param bool $result
     * @return JsonResponse
     */
    public function jsonResponse(bool $result): JsonResponse
    {
        return response()->json([
            'success' => $result,
            'message' => 'Course deleted successfully',
        ]);
    }
}
