<?php

namespace App\Validator;

use App\Entity\StudyGroup;
use App\Validator\Exception\ValidationException;

class StudyGroupValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(StudyGroup $entity): void
    {
        $errorMessage = '';

        if (\strlen($entity->getName()) > 10) {
            $errorMessage .= "The maximum lenght of the name is 10 characters.\n";
        }

        if (\strlen($entity->getSpecialization()) < 2 || \strlen($entity->getSpecialization()) > 100) {
            $errorMessage .= "The specialization length needs to be between 2 and 100 characters.\n";
        }

        if ($entity->getNumber() < 0 || $entity->getNumber() > 13) {
            $errorMessage .= "The number needs to be between 0 and 13.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }
}