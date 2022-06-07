<?php

namespace App\Dto\ViewModel;

class StudyGroupViewModel
{
    public function __construct(
       private int $id,
       private int $number,
       private string $name,
       private string $specialization,
       private UserViewModel $classMaster,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassMaster(): UserViewModel
    {
        return $this->classMaster;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }
}
