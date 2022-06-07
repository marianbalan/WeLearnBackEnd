<?php

namespace App\Dto\Dto;

class SchoolDto
{
    /**
     * @param array<string, string> $location
     */
    public function __construct(
        private string $name,
        private string $cui,
        private array $location,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCui(): string
    {
        return $this->cui;
    }

    /**
     * @return array<string, string>
     */
    public function getLocation(): array
    {
        return $this->location;
    }
}
