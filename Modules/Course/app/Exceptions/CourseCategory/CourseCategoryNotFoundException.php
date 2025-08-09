<?php

namespace Modules\Course\Exceptions\CourseCategory;

class CourseCategoryNotFoundException extends CourseCategoryException
{
    /**
     * Create a new exception for a course category that was not found.
     *
     * @param int $id The ID of the course category that was not found
     * @return static
     */
    public static function forId(int $id): static
    {
        return new static(
            "Course category with ID {$id} not found.",
            404
        );
    }

    /**
     * Create a new exception for a course category that was not found by slug.
     *
     * @param string $slug The slug of the course category that was not found
     * @return static
     */
    public static function forSlug(string $slug): static
    {
        return new static(
            "Course category with slug '{$slug}' not found.",
            404
        );
    }
}
