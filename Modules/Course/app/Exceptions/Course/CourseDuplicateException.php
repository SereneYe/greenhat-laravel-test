<?php

namespace Modules\Course\Exceptions\Course;

class CourseDuplicateException extends CourseException
{
    /**
     * Create a new exception for a duplicate course title.
     *
     * @param string $title The duplicate course title
     * @return static
     */
    public static function duplicateTitle(string $title): static
    {
        return new static(
            "A course with the title '{$title}' already exists.",
            422,
            [
                'errors' => [
                    'title' => ['This course title already exists']
                ]
            ]
        );
    }

    /**
     * Create a new exception for a duplicate course slug.
     *
     * @param string $slug The duplicate course slug
     * @return static
     */
    public static function duplicateSlug(string $slug): static
    {
        return new static(
            "A course with the slug '{$slug}' already exists.",
            422,
            [
                'errors' => [
                    'slug' => ['This course slug already exists']
                ]
            ]
        );
    }
}
