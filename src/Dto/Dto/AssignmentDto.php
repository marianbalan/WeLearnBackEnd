<?php

namespace App\Dto\Dto;

class AssignmentDto
{
    public function __construct(
        private string $title,
        private ?string $description,
        private int $date,
        private int $dueTo,
        private int $subjectId
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getDueTo(): int
    {
        return $this->dueTo;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }
}