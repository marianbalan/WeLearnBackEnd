<?php

namespace App\Dto\ViewModel;

class NonAttendanceViewModel
{
    public function __construct(
        private int $id,
        private int $date,
        private bool $motivated,
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

    public function isMotivated(): bool
    {
        return $this->motivated;
    }
}