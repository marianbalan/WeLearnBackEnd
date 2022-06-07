<?php

namespace App\Dto\Dto;

class SubjectDto
{
    public function __construct(
        private string $name,
        private int $studyGroupId,
        private int $teacherId,
        private ?int $id = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStudyGroupId(): int
    {
        return $this->studyGroupId;
    }

    public function getTeacherId(): int
    {
        return $this->teacherId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}