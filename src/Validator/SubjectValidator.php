<?php

namespace App\Validator;

use App\Entity\Subject;
use App\Validator\Exception\ValidationException;

class SubjectValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(Subject $entity): void
    {
        $errorMessage = '';

        if (\strlen($entity->getName()) < 2 || \strlen($entity->getName()) > 100) {
            $errorMessage .= "The name length needs to be between 2 and 30 characters.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }
}