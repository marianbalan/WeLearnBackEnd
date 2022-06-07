<?php

namespace App\Dto\ViewModel;

class StudentSituationViewModel
{
    /**
     * @param GradeViewModel[] $grades
     * @param NonAttendanceViewModel[] $nonAttendances
     */
    public function __construct(
        private SubjectViewModel $subject,
        private array $grades,
        private array $nonAttendances,
        private ?int $averageScore,
    ) {
    }
    public function getSubject(): SubjectViewModel
    {
        return $this->subject;
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