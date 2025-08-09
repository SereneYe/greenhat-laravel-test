<?php

namespace Modules\Course\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use League\Fractal\Serializer\DataArraySerializer;
use Lorisleiva\Actions\ActionRequest;
use Modules\Course\Actions\CourseEnrollment\EnrollEmployeeInCourse;
use Modules\Course\Actions\CourseEnrollment\GetEmployeeCourseEnrollments;
use Modules\Course\Actions\CourseEnrollment\UnenrollEmployeeFromCourse;
use Modules\Course\Exceptions\CourseEnrollment\EmployeeAlreadyEnrolledException;
use Modules\Course\Transformers\CourseEnrollmentTransformer;

class CourseEnrollmentController extends Controller
{
    /**
     * Enroll the authenticated employee in a course.
     *
     * @param ActionRequest $request
     * @param int $courseId
     * @return array|JsonResponse
     */
    public function enroll(ActionRequest $request, int $courseId): array|JsonResponse
    {
        try {
            $enrollment = (new EnrollEmployeeInCourse())->asController($request, $courseId);

            return fractal($enrollment, new CourseEnrollmentTransformer())
                ->includeCourse()
                ->includeEmployee()
                ->serializeWith(new DataArraySerializer())
                ->toArray();

        } catch (EmployeeAlreadyEnrolledException $e) {
            return response()->json([
                'message' => 'You are already enrolled in this course.',
                'error' => 'already_enrolled'
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'enrollment_failed'
            ], 400);
        }
    }

    /**
     * Unenroll the authenticated employee from a course.
     *
     * @param ActionRequest $request
     * @param int $courseId
     * @return JsonResponse
     */
    public function unenroll(ActionRequest $request, int $courseId): JsonResponse
    {
        return (new UnenrollEmployeeFromCourse())->asController($request, $courseId);
    }

    /**
     * Get the authenticated employee's course enrollments.
     *
     * @param ActionRequest $request
     * @return array
     */
    public function myEnrollments(ActionRequest $request): array
    {
        $enrollments = (new GetEmployeeCourseEnrollments())->asController($request);

        return fractal($enrollments, new CourseEnrollmentTransformer())
            ->includeCourse()
            ->includeEmployee()
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }

    /**
     * Get a specific employee's course enrollments (admin/manager access).
     *
     * @param ActionRequest $request
     * @param int $employeeId
     * @return array
     */
    public function employeeEnrollments(ActionRequest $request, int $employeeId): array
    {
        $enrollments = (new GetEmployeeCourseEnrollments())->asControllerForEmployee($request, $employeeId);

        return fractal($enrollments, new CourseEnrollmentTransformer())
            ->includeCourse()
            ->includeEmployee()
            ->serializeWith(new DataArraySerializer())
            ->toArray();
    }
}
