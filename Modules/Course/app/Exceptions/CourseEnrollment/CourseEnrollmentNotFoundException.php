<?php

namespace Modules\Course\Exceptions\CourseEnrollment;

class CourseEnrollmentNotFoundException extends CourseEnrollmentException
{
    public static function forCourse(int $employeeId, int $courseId, string $courseTitle): static
    {
        return new static(
            "Employee {$employeeId} is not enrolled in the course '{$courseTitle}' or enrollment is already cancelled.",
            404,
            [
                'errors' => [
                    'enrollment' => ['No active enrollment found for this course']
                ]
            ]
        );
    }

    public static function simple(string $courseTitle): static
    {
        return new static(
            "You are not enrolled in the course '{$courseTitle}' or your enrollment is already cancelled.",
            404,
            [
                'errors' => [
                    'enrollment' => ['No active enrollment found for this course']
                ]
            ]
        );
    }

    public static function byId(int $enrollmentId): static
    {
        return new static(
            "Course enrollment with ID {$enrollmentId} not found.",
            404
        );
    }
}
