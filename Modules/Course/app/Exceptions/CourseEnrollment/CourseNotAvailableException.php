<?php

namespace Modules\Course\Exceptions\CourseEnrollment;

class CourseNotAvailableException extends CourseEnrollmentException
{
    public static function deleted(string $courseTitle): static
    {
        return new static(
            "The course '{$courseTitle}' is no longer available for enrollment as it has been deleted.",
            422,
            [
                'errors' => [
                    'course_id' => ['This course is no longer available for enrollment']
                ]
            ]
        );
    }

    public static function notFound(int $courseId): static
    {
        return new static(
            "Course with ID {$courseId} not found.",
            404,
            [
                'errors' => [
                    'course_id' => ['The selected course does not exist']
                ]
            ]
        );
    }

    public static function inactive(string $courseTitle): static
    {
        return new static(
            "The course '{$courseTitle}' is currently inactive and not available for enrollment.",
            422,
            [
                'errors' => [
                    'course_id' => ['This course is currently not available for enrollment']
                ]
            ]
        );
    }
}
