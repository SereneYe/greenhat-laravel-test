<?php

namespace Modules\Course\Exceptions\Course;

class CourseValidationException extends CourseException
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
     * Create a new exception for an invalid course level.
     *
     * @param string $level The invalid level
     * @param array $allowedLevels The allowed levels
     * @return static
     */
    public static function invalidLevel(string $level, array $allowedLevels): static
    {
        return new static(
            "The course level '{$level}' is invalid. Allowed levels are: " . implode(', ', $allowedLevels),
            422,
            [
                'errors' => [
                    'level' => ['The selected level is invalid. Allowed levels are: ' . implode(', ', $allowedLevels)]
                ]
            ]
        );
    }

    /**
     * Create a new exception for an invalid media file.
     *
     * @param int $mediaId The invalid media ID
     * @return static
     */
    public static function invalidMedia(int $mediaId): static
    {
        return new static(
            "The media file with ID {$mediaId} does not exist or is not valid.",
            422,
            [
                'errors' => [
                    'cover_media_id' => ['The selected media file does not exist or is not valid.']
                ]
            ]
        );
    }

    /**
     * Create a new exception for invalid category IDs.
     *
     * @param array $invalidIds The invalid category IDs
     * @return static
     */
    public static function invalidCategories(array $invalidIds): static
    {
        return new static(
            "The following category IDs are invalid: " . implode(', ', $invalidIds),
            422,
            [
                'errors' => [
                    'categories' => ['The following category IDs are invalid: ' . implode(', ', $invalidIds)]
                ]
            ]
        );
    }
}
