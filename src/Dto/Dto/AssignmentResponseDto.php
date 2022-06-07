<?php

namespace App\Dto\Dto;

class AssignmentResponseDto
{
    public function __construct(
        private int $date,
        private int $assignmentId,
        private int $studentId,
    ) {
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getAssignmentId(): int
    {
        return $this->assignmentId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }
}