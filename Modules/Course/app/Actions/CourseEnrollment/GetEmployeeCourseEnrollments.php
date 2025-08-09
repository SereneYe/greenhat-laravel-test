<?php

namespace Modules\Course\Actions\CourseEnrollment;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\CourseEnrollment\CourseEnrollmentFiltersData;
use Modules\Course\Models\CourseEnrollment;
use Modules\Course\Transformers\CourseEnrollmentTransformer;
use Modules\Employee\Models\Employee;
use Spatie\Fractal\Fractal;

class GetEmployeeCourseEnrollments
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param Employee $employee
     * @param CourseEnrollmentFiltersData $filters
     * @return LengthAwarePaginator
     */
    public function handle(Employee $employee, CourseEnrollmentFiltersData $filters): LengthAwarePaginator
    {
        $query = $employee->courseEnrollments()->with(['course', 'course.categories']);

        // Apply status filter
        if ($filters->status && !$filters->status instanceof \Spatie\LaravelData\Optional) {
            if ($filters->status === 'active') {
                $query->whereNull('cancelled_at');
            } elseif ($filters->status === 'cancelled') {
                $query->whereNotNull('cancelled_at');
            }
        }

        // Apply date range filter
        if ($filters->date_from && !$filters->date_from instanceof \Spatie\LaravelData\Optional) {
            $query->whereDate('enrolled_at', '>=', $filters->date_from);
        }

        if ($filters->date_to && !$filters->date_to instanceof \Spatie\LaravelData\Optional) {
            $query->whereDate('enrolled_at', '<=', $filters->date_to);
        }

        // Apply category filter - Fix for the main error
        $categoryIds = $filters->getCategoryIds();
        if (!empty($categoryIds)) {
            $query->whereHas('course.categories', function ($q) use ($categoryIds) {
                $q->whereIn('course_categories.id', $categoryIds);
            });
        }

        // Apply sorting
        $sortBy = $filters->sort_by instanceof \Spatie\LaravelData\Optional ? 'enrolled_at' : ($filters->sort_by ?? 'enrolled_at');
        $sortDirection = $filters->sort_direction instanceof \Spatie\LaravelData\Optional ? 'desc' : ($filters->sort_direction ?? 'desc');

        switch ($sortBy) {
            case 'course_title':
                $query->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                    ->orderBy('courses.title', $sortDirection)
                    ->select('course_enrollments.*');
                break;
            case 'enrolled_at':
            case 'created_at':
            default:
                $query->orderBy($sortBy, $sortDirection);
                break;
        }

        // Apply pagination
        $perPage = $filters->per_page instanceof \Spatie\LaravelData\Optional ? 15 : ($filters->per_page ?? 15);

        return $query->paginate($perPage);
    }

    /**
     * Handle the action as a controller for the authenticated user's enrollments.
     *
     * @param ActionRequest $request
     * @return LengthAwarePaginator
     */
    public function asController(ActionRequest $request): LengthAwarePaginator
    {
        $filters = CourseEnrollmentFiltersData::from($request->all());
        $employee = $request->user()->employee ?? Employee::where('user_id', $request->user()->id)->firstOrFail();

        return $this->handle($employee, $filters);
    }

    /**
     * Handle the action as a controller for a specific employee's enrollments.
     *
     * @param ActionRequest $request
     * @param int $employeeId
     * @return LengthAwarePaginator
     */
    public function asControllerForEmployee(ActionRequest $request, int $employeeId): LengthAwarePaginator
    {
        $filters = CourseEnrollmentFiltersData::from($request->all());
        $employee = Employee::findOrFail($employeeId);

        return $this->handle($employee, $filters);
    }

    /**
     * Format the response.
     *
     * @param LengthAwarePaginator $enrollments
     * @return Fractal
     */
    public function jsonResponse(LengthAwarePaginator $enrollments): Fractal
    {
        return fractal($enrollments, new CourseEnrollmentTransformer())
            ->includeCourse()
            ->includeEmployee();
    }
}
