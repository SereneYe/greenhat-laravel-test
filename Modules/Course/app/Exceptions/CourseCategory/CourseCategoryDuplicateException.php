<?php

namespace Modules\Course\Exceptions\CourseCategory;

class CourseCategoryDuplicateException extends CourseCategoryException
{
    /**
     * Create a new exception for a duplicate category name.
     *
     * @param string $name The duplicate category name
     * @return static
     */
    public static function duplicateName(string $name): static
    {
        return new static(
            "A category with the name '{$name}' already exists.",
            422,
            [
                'errors' => [
                    'name' => ['This category name already exists']
                ]
            ]
        );
    }

    /**
     * Create a new exception for a duplicate category slug.
     *
     * @param string $slug The duplicate category slug
     * @return static
     */
    public static function duplicateSlug(string $slug): static
    {
        return new static(
            "A category with the slug '{$slug}' already exists.",
            422,
            [
                'errors' => [
                    'slug' => ['This category slug already exists']
                ]
            ]
        );
    }
}
