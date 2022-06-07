<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\SchoolDto;
use App\Entity\School;

class SchoolMapper
{
    public static function schoolToSchoolDto(School $school): SchoolDto
    {
        return new SchoolDto(
            name: $school->getName(),
            cui: $school->getCui(),
            location: $school->getLocation(),
        );
    }

    public static function schoolDtoToSchool(SchoolDto $schoolDto): School
    {
        return new School(
            name: $schoolDto->getName(),
            cui: $schoolDto->getCui(),
            location: $schoolDto->getLocation(),
        );
    }
}
