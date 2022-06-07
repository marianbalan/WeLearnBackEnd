<?php

namespace App\Dto\ViewModel;

class SubjectViewModel
{
    public function __construct(
        private int $id,
        private string $name,
        private UserViewModel $teacher,
        private StudyGroupViewModel $studyGroup,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTeacher(): UserViewModel
    {
        return $this->teacher;
    }

    public function getStudyGroup(): StudyGroupViewModel
    {
        return $this->studyGroup;
    }
}