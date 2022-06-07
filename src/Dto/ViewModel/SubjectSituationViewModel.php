<?php

namespace App\Dto\ViewModel;

class SubjectSituationViewModel
{
    /**
     * @param GradeViewModel[] $grades
     * @param NonAttendanceViewModel[] $nonAttendances
     */
    public function __construct(
        private UserViewModel $user,
        private array $grades,
        private array $nonAttendances,
        private ?int $averageScore
    ) {
    }

    /**
     * @return UserViewModel
     */
    public function getUser(): UserViewModel
    {
        return $this->user;
    }

    /**
     * @return GradeViewModel[]
     */
    public function getGrades(): array
    {
        return $this->grades;
    }

    /**
     * @return NonAttendanceViewModel[]
     */
    public function getNonAttendances(): array
    {
        return $this->nonAttendances;
    }

    public function getAverageScore(): ?int
    {
        return $this->averageScore;
    }
}