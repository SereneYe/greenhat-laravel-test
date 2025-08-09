<?php

namespace Modules\Course\Exceptions\CourseCategory;

class CourseCategoryValidationException extends CourseCategoryException
{
    /**
     * Create a new exception for validation errors.
     *
     * @param array $errors The validation errors
     * @return static
     */
    public static function withErrors(array $errors): static
    {
        return new static(
            'The given data was invalid.',
            422,
            ['errors' => $errors]
        );
    }

    /**
     * Create a new exception for an invalid name.
     *
     * @param string $message The error message
     * @return static
     */
    public static function invalidName(string $message = 'The category name is invalid.'): static
    {
        return new static(
            $message,
            422,
            [
                'errors' => [
                    'name' => [$message]
                ]
            ]
        );
    }

    /**
     * Create a new exception for an invalid color.
     *
     * @param string $color The invalid color
     * @return static
     */
    public static function invalidColor(string $color): static
    {
        return new static(
            "The color '{$color}' is not a valid hexadecimal color code.",
            422,
            [
                'errors' => [
                    'color' => ['The color must be a valid hexadecimal color code (e.g., #FF5733).']
                ]
            ]
        );
    }
}
