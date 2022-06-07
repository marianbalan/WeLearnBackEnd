<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\NonAttendanceDto;
use App\Dto\ViewModel\NonAttendanceViewModel;
use App\Entity\NonAttendance;

class NonAttendanceMapper
{
    public static function nonAttendanceToNonAttendanceDto(NonAttendance $nonAttendance): NonAttendanceDto
    {
        return new NonAttendanceDto(
            date: $nonAttendance->getDate()->getTimestamp(),
            subjectId: $nonAttendance->getSubject()->getId(),
            studentId: $nonAttendance->getStudent()->getId(),
            motivated: $nonAttendance->getMotivated(),
            id: $nonAttendance->getId()
        );
    }

    public static function nonAttendanceDtoToNonAttendance(NonAttendanceDto $nonAttendanceDto): NonAttendance
    {
        return new NonAttendance(
            date: (new \DateTime())->setTimestamp($nonAttendanceDto->getDate()),
            motivated: $nonAttendanceDto->isMotivated(),
        );
    }

    public static function nonAttendanceToNonAttendanceViewModel(NonAttendance $nonAttendance): NonAttendanceViewModel
    {
        return new NonAttendanceViewModel(
            id: $nonAttendance->getId(),
            date: $nonAttendance->getDate()->getTimestamp(),
            motivated: $nonAttendance->getMotivated(),
        );
    }

    /**
     * @param NonAttendance[] $nonAttendances
     * @return NonAttendanceViewModel[]
     */
    public static function nonAttendancesToNonAttendanceViewModels(array $nonAttendances): array
    {
        return \array_map(
            fn (NonAttendance $nonAttendance) => self::nonAttendanceToNonAttendanceViewModel($nonAttendance),
            $nonAttendances
        );
    }
}