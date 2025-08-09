<?php

namespace Modules\Course\Exceptions\CourseEnrollment;

class EmployeeAlreadyEnrolledException extends CourseEnrollmentException
{
    public static function forCourse(int $employeeId, int $courseId, string $courseTitle): static
    {
        return new static(
            "Employee {$employeeId} is already enrolled in the course '{$courseTitle}'.",
            422,
            [
                'errors' => [
                    'course_id' => ['You are already enrolled in this course']
                ]
            ]
        );
    }

    public static function simple(string $courseTitle): static
    {
        return new static(
            "You are already enrolled in the course '{$courseTitle}'.",
            422,
            [
                'errors' => [
                    'enrollment' => ['Already enrolled in this course']
                ]
            ]
        );
    }
}
