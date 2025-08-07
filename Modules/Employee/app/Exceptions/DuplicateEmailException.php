<?php

namespace Modules\Employee\Exceptions;

class DuplicateEmailException extends EmployeeException
{
    /**
     * Create a new exception for an email that already exists.
     *
     * @param string $email The email that already exists
     * @return static
     */
    public static function emailAlreadyExists(string $email): static
    {
        return new static(
            "The email {$email} has already been registered.",
            422,
            [
                'errors' => [
                    'email' => ['This email has already been registered']
                ]
            ]
        );
    }
}
