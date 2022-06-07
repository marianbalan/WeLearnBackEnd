<?php

namespace App\Dto\Dto;

class NlpInput implements \JsonSerializable
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $pin,
        private string $filePath
    ) {
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

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'pin' => $this->getPin(),
            'filePath' => $this->getFilePath(),
        ];
    }

    /**
     * @param string $firstName
     * @return NlpInput
     */
    public function setFirstName(string $firstName): NlpInput
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string $lastName
     * @return NlpInput
     */
    public function setLastName(string $lastName): NlpInput
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string $pin
     * @return NlpInput
     */
    public function setPin(string $pin): NlpInput
    {
        $this->pin = $pin;
        return $this;
    }

    /**
     * @param string $filePath
     * @return NlpInput
     */
    public function setFilePath(string $filePath): NlpInput
    {
        $this->filePath = $filePath;
        return $this;
    }


}