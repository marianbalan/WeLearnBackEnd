<?php

namespace App\Dto\ViewModel;

class GradeViewModel
{
    public function __construct(
        private int $id,
        private int $grade,
        private int $date,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGrade(): int
    {
        return $this->grade;
    }

    public function getDate(): int
    {
        return $this->date;
    }
}