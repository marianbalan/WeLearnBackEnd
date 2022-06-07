<?php

namespace App\Dto\Dto;

class GradeDto
{
    public function __construct(
        private int $grade,
        private int $date,
        private int $subjectId,
        private int $studentId,
        private ?int $id = null,
    ) {
    }

    public function getGrade(): int
    {
        return $this->grade;
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

    public function getId(): ?int
    {
        return $this->id;
    }
}
