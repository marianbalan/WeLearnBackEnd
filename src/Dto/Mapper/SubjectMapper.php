<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\SubjectDto;
use App\Dto\ViewModel\SubjectViewModel;
use App\Entity\Subject;

class SubjectMapper
{
    public static function subjectToSubjectDto(Subject $subject): SubjectDto
    {
        return new SubjectDto(
            name: $subject->getName(),
            studyGroupId: $subject->getStudyGroup()->getId(),
            teacherId: $subject->getTeacher()->getId(),
            id: $subject->getId()
        );
    }

    public static function subjectDtoToSubject(SubjectDto $subjectDto): Subject
    {
        return new Subject(
            name: $subjectDto->getName()
        );
    }

    public static function subjectToSubjectViewModel(Subject $subject): SubjectViewModel
    {
        return new SubjectViewModel(
            id: $subject->getId(),
            name: $subject->getName(),
            teacher: UserMapper::userToUserViewModel($subject->getTeacher()),
            studyGroup: StudyGroupMapper::studyGroupToStudyGroupViewModel($subject->getStudyGroup())
        );
    }

    /**
     * @param Subject[] $subjects
     * @return SubjectViewModel[]
     */
    public static function subjectsToSubjectViewModels(array $subjects): array
    {
        return \array_map(
            fn (Subject $subject) => self::subjectToSubjectViewModel($subject),
            $subjects
        );
    }
}