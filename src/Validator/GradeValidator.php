<?php

namespace App\Validator;

use App\Entity\Grade;
use App\Validator\Exception\ValidationException;

class GradeValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(Grade $entity): void
    {
        $errorMessage = '';

        if ($entity->getGrade() < 1 || $entity->getGrade() > 10) {
            $errorMessage .= "The grade needs to be between 1 and 10.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }

    public function validateGradeMark(int $grade): void
    {
        if ($grade < 1 || $grade > 10) {
            throw new ValidationException("The grade needs to be between 1 and 10.");
        }
    }
}