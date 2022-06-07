<?php

namespace App\Dto\ViewModel;

class UserViewModel
{
    public function __construct(
        private int $id,
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $pin,
        private string $phoneNumber,
        private string $activated,
        private ?StudyGroupViewModel $studyGroup = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getActivated(): string
    {
        return $this->activated;
    }

    public function getStudyGroup(): ?StudyGroupViewModel
    {
        return $this->studyGroup;
    }

    public function setStudyGroup(?StudyGroupViewModel $studyGroup): self
    {
        $this->studyGroup = $studyGroup;

        return $this;
    }
}