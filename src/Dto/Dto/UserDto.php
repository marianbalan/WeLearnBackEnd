<?php

namespace App\Dto\Dto;

class UserDto
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private ?int $id,
        private string $email,
        private ?string $password,
        private string $firstName,
        private string $lastName,
        private string $pin,
        private string $phoneNumber,
        private array $roles = ['ROLE_USER'],
        private ?int $studyGroupId = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getStudyGroupId(): ?int
    {
        return $this->studyGroupId;
    }

    public function eraseCredentials(): self
    {
        $this->password = null;

        return $this;
    }
}
