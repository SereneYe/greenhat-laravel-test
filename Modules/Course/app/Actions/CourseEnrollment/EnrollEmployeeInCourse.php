<?php

namespace Modules\Course\Actions\CourseEnrollment;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\CourseEnrollment\EnrollEmployeeInCourseData;
use Modules\Course\Exceptions\CourseEnrollment\CourseNotAvailableException;
use Modules\Course\Exceptions\CourseEnrollment\EmployeeAlreadyEnrolledException;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseEnrollment;
use Modules\Employee\Models\Employee;
use Modules\Course\Transformers\CourseEnrollmentTransformer;
use Spatie\Fractal\Fractal;
use Illuminate\Support\Facades\Log;

class EnrollEmployeeInCourse
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param Employee $employee
     * @param Course $course
     * @param EnrollEmployeeInCourseData|null $data
     * @return CourseEnrollment
     * @throws CourseNotAvailableException
     * @throws EmployeeAlreadyEnrolledException
     */
    public function handle(Employee $employee, Course $course, ?EnrollEmployeeInCourseData $data = null): CourseEnrollment
    {
        // Check if course is available (not deleted)
        if ($course->trashed()) {
            throw CourseNotAvailableException::deleted($course->title);
        }

        // Check if employee is already enrolled in this course
        if ($employee->isEnrolledIn($course)) {
            throw EmployeeAlreadyEnrolledException::simple($course->title);
        }

        // Enroll the employee in the course
        $enrollment = $employee->enrollInCourse($course);

        // Add notes if provided
        if ($data && $data->notes && !$data->notes instanceof \Spatie\LaravelData\Optional) {
            $enrollment->update(['notes' => $data->notes]);
        }

        // Send enrollment confirmation email
        $this->sendEnrollmentConfirmationEmail($enrollment);

        return $enrollment;
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @param int $courseId
     * @return CourseEnrollment
     */
    public function asController(ActionRequest $request, int $courseId): CourseEnrollment
    {
        $data = EnrollEmployeeInCourseData::validateAndCreate($request->all());
        $course = Course::findOrFail($courseId);
        $employee = $request->user()->employee ?? Employee::where('user_id', $request->user()->id)->firstOrFail();

        return $this->handle($employee, $course, $data);
    }

    /**
     * Format the response.
     *
     * @param CourseEnrollment $enrollment
     * @return Fractal
     */
    public function jsonResponse(CourseEnrollment $enrollment): Fractal
    {
        return fractal($enrollment, new CourseEnrollmentTransformer())
            ->includeCourse()
            ->includeEmployee();
    }

    /**
     * Send enrollment confirmation email
     */
    private function sendEnrollmentConfirmationEmail(CourseEnrollment $enrollment): void
    {
        try {
            // Dispatch email job
            \App\Jobs\SendCourseEnrollmentConfirmationEmailJob::dispatch($enrollment);

            Log::info('Course enrollment confirmation email queued successfully', [
                'enrollment_id' => $enrollment->id,
                'employee_id' => $enrollment->employee_id,
                'course_id' => $enrollment->course_id,
                'email' => $enrollment->employee->user->email
            ]);

        } catch (\Exception $e) {
            // Don't fail the enrollment if email fails
            Log::error('Course enrollment confirmation email queuing failed', [
                'enrollment_id' => $enrollment->id,
                'employee_id' => $enrollment->employee_id,
                'course_id' => $enrollment->course_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
