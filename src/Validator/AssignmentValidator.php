<?php

namespace App\Validator;

use App\Dto\Dto\AssignmentDto;
use App\Validator\Exception\ValidationException;

class AssignmentValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(AssignmentDto $entity): void
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