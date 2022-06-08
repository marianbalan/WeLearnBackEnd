<?php

namespace App\Validator;

use App\Entity\Assignment;
use App\Validator\Exception\ValidationException;

class AssignmentValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(Assignment $entity): void
    {
        $errorMessage = '';

        if (\strlen($entity->getTitle()) < 3 || \strlen($entity->getTitle()) > 50) {
            $errorMessage .= "Assignment title needs to have between 3 and 50 characters.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }
}