<?php

namespace Modules\Course\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Modules\Course\Models\CourseEnrollment;

class CourseEnrollmentTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'course',
        'employee',
    ];

    /**
     * Transform the CourseEnrollment model.
     *
     * @param CourseEnrollment $model
     * @return array
     */
    public function transform($model): array
    {
        $data = $model instanceof CourseEnrollment ? $model : new CourseEnrollment;

        return [
            'id' => $data->getKey(),
            'employeeId' => $data->employee_id,
            'courseId' => $data->course_id,
            'enrolledAt' => $data->enrolled_at?->toIso8601String(),
            'cancelledAt' => $data->cancelled_at?->toIso8601String(),
            'status' => $data->cancelled_at ? 'cancelled' : 'active',
            'notes' => $data->notes,
            'createdAt' => $data->created_at?->toIso8601String(),
            'updatedAt' => $data->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Include course in the transformation.
     *
     * @param CourseEnrollment $model
     * @return Item|null
     */
    public function includeCourse(CourseEnrollment $model): ?Item
    {
        if (!$model->course) {
            return null;
        }

        return $this->item($model->course, new CourseTransformer);
    }

    /**
     * Include employee in the transformation.
     *
     * @param CourseEnrollment $model
     * @return Item|null
     */
    public function includeEmployee(CourseEnrollment $model): ?Item
    {
        if (!$model->employee) {
            return null;
        }

        // Simple employee data transformation
        return $this->item($model->employee, function ($employee) {
            return [
                'id' => $employee->getKey(),
                'userId' => $employee->user_id,
                'username' => $employee->username,
                'createdAt' => $employee->created_at?->toIso8601String(),
                'updatedAt' => $employee->updated_at?->toIso8601String(),
            ];
        });
    }
}
