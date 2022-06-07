<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\GradeDto;
use App\Dto\ViewModel\GradeViewModel;
use App\Entity\Grade;

class GradeMapper
{
    public static function gradeToGradeDto(Grade $grade): GradeDto
    {
        return new GradeDto(
            grade: $grade->getGrade(),
            date: $grade->getDate()->getTimestamp(),
            subjectId: $grade->getStudent()->getId(),
            studentId: $grade->getStudent()->getId(),
            id: $grade->getId()
        );
    }

    public static function gradeDtoToGrade(GradeDto $gradeDto): Grade
    {
        return new Grade(
            grade: $gradeDto->getGrade(),
            date: (new \DateTime())->setTimestamp($gradeDto->getDate())
        );
    }

    public static function gradeToGradeViewModel(Grade $grade): GradeViewModel
    {
        return new GradeViewModel(
            id: $grade->getId(),
            grade: $grade->getGrade(),
            date: $grade->getDate()->getTimestamp(),
        );
    }

    /**
     * @param Grade[] $grades
     * @return GradeViewModel[]
     */
    public static function gradesToGradeViewModels(array $grades): array
    {
        return \array_map(
            fn(Grade $grade) => self::gradeToGradeViewModel($grade),
            $grades
        );
    }
}