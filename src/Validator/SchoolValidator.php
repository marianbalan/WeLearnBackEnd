<?php

namespace App\Validator;

use App\Dto\Dto\SchoolDto;
use App\Validator\Exception\ValidationException;

class SchoolValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(SchoolDto $entity): void
    {
        $errorMessage = '';

        if (\strlen($entity->getName()) < 5 || \strlen($entity->getName()) > 100) {
            $errorMessage .= "School length needs to be between 5 and 100 characters.\n";
        }

        if (
            false === \array_key_exists('county', $entity->getLocation()) ||
            false === \array_key_exists('city', $entity->getLocation()) ||
            false === \array_key_exists('street', $entity->getLocation()) ||
            false === \array_key_exists('number', $entity->getLocation())
        ) {
            $errorMessage .= "The entire location of the school needs to be provided.\n
                This includes the county, city, street and number.\n";
        }

        if (\strlen($entity->getLocation()['county']) < 2 || \strlen($entity->getLocation()['county']) > 50) {
            $errorMessage .= "The school county length needs to be between 2 and 50 characters.\n";
        }
        if (\strlen($entity->getLocation()['city']) < 2 || \strlen($entity->getLocation()['city']) > 50) {
            $errorMessage .= "The school city length needs to be between 2 and 50 characters.\n";
        }
        if (\strlen($entity->getLocation()['street']) < 2 || \strlen($entity->getLocation()['street']) > 50) {
            $errorMessage .= "The school street length needs to be between 2 and 50 characters.\n";
        }
        if (\strlen($entity->getLocation()['number']) < 2 || \strlen($entity->getLocation()['number']) > 50) {
            $errorMessage .= "The school number length needs to be between 2 and 50 characters.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }
}