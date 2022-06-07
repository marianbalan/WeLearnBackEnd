<?php

namespace App\Dto\Dto;

class NonAttendanceDto
{
    public function __construct(
        private int $date,
        private int $subjectId,
        private int $studentId,
        private bool $motivated,
        private ?int $id = null,
    ) {
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function isMotivated(): bool
    {
        return $this->motivated;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
