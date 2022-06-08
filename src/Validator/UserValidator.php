<?php

namespace App\Validator;

use App\Entity\User;
use App\Validator\Exception\ValidationException;

class UserValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(User $entity): void
    {
        $errorMessage = '';

        if (!filter_var($entity->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errorMessage .= "Invalid email format.\n";
        }

        if (
            !preg_match("/^[a-zA-Z-' ]*$/", $entity->getFirstName()) ||
            !preg_match("/^[a-zA-Z-' ]*$/", $entity->getLastName())
        ) {
            $errorMessage .= "Only letters and white space are allowed for the firstname and lastname.\n";
        }

        if (
            false === \is_numeric($entity->getPin()) ||
            \strlen($entity->getPin()) < 5 ||
            \strlen($entity->getPin())> 50
        ) {
            $errorMessage .= "The pin must be a 5-50 digits long number.\n";
        }

        if (
            false === \is_numeric($entity->getPhoneNumber()) ||
            \strlen($entity->getPin()) < 5 ||
            \strlen($entity->getPin())> 50
        ) {
            $errorMessage .= "The phone number must be a 5-50 digits long number.\n";
        }

        if ('' !== $errorMessage) {
            throw new ValidationException($errorMessage);
        }
    }
}