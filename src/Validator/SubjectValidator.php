<?php

namespace App\Validator;

use App\Dto\Dto\SubjectDto;
use App\Validator\Exception\ValidationException;

class SubjectValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(SubjectDto $entity): void
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