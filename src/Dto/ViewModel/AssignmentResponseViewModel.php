<?php

namespace App\Dto\ViewModel;

class AssignmentResponseViewModel
{
    public function __construct(
        private int $id,
        private int $date,
        private int $assignmentId,
        private UserViewModel $student,
        private ?string $filePath
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getAssignmentId(): int
    {
        return $this->assignmentId;
    }

    public function getStudent(): UserViewModel
    {
        return $this->student;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}