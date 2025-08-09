<?php

namespace Modules\Course\Exceptions\Course;

class CourseNotFoundException extends CourseException
{
    /**
     * Create a new exception for a course that was not found by ID.
     *
     * @param int $id The course ID that was not found
     * @return static
     */
    public static function forId(int $id): static
    {
        return new static(
            "Course with ID {$id} not found.",
            404
        );
    }

    /**
     * Create a new exception for a course that was not found by slug.
     *
     * @param string $slug The course slug that was not found
     * @return static
     */
    public static function forSlug(string $slug): static
    {
        return new static(
            "Course with slug '{$slug}' not found.",
            404
        );
    }
}
