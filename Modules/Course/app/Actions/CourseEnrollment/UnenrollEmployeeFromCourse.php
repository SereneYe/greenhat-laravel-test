<?php

namespace Modules\Course\Actions\CourseEnrollment;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Exceptions\CourseEnrollment\CourseEnrollmentNotFoundException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseEnrollment;
use Modules\Employee\Models\Employee;
use Illuminate\Http\JsonResponse;

class UnenrollEmployeeFromCourse
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param Employee $employee
     * @param Course $course
     * @return CourseEnrollment
     * @throws CourseEnrollmentNotFoundException
     */
    public function handle(Employee $employee, Course $course): CourseEnrollment
    {
        // Find the active enrollment
        $enrollment = $employee->courseEnrollments()
            ->where('course_id', $course->id)
            ->whereNull('cancelled_at')
            ->first();

        if (!$enrollment) {
            throw CourseEnrollmentNotFoundException::simple($course->title);
        }

        // Cancel the enrollment
        $enrollment->cancel();

        return $enrollment;
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $courseId
     * @return JsonResponse
     */
    public function asController(ActionRequest $request, int $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $employee = $request->user()->employee ?? Employee::where('user_id', $request->user()->id)->firstOrFail();

        $this->handle($employee, $course);

        return response()->json([
            'message' => 'Successfully unenrolled from the course.',
        ]);
    }
}
