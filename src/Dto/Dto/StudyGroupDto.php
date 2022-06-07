<?php

namespace App\Dto\Dto;

class StudyGroupDto
{
    public function __construct(
        private string $name,
        private int $number,
        private string $specialization,
        private int $schoolId,
        private ?int $classMasterId,
        private ?int $id = -1,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getSchoolId(): int
    {
        return $this->schoolId;
    }

    public function getClassMasterId(): int
    {
        return $this->classMasterId;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }
}
