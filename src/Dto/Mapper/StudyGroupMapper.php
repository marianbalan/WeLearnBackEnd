<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\StudyGroupDto;
use App\Dto\ViewModel\StudyGroupViewModel;
use App\Entity\StudyGroup;

class StudyGroupMapper
{
    public static function studyGroupToStudyGroupDto(StudyGroup $studyGroup): StudyGroupDto
    {
        return new StudyGroupDto(
            name: $studyGroup->getName(),
            number: $studyGroup->getNumber(),
            specialization: $studyGroup->getSpecialization(),
            schoolId: $studyGroup->getSchool()->getId(),
            classMasterId: $studyGroup->getClassMaster()->getId(),
            id: $studyGroup->getId()
        );
    }

    public static function studyGroupDtoToStudyGroup(StudyGroupDto $studyGroupDto): StudyGroup
    {
        return new StudyGroup(
            name: $studyGroupDto->getName(),
            number: $studyGroupDto->getNumber(),
            specialization: $studyGroupDto->getSpecialization(),
        );
    }

    public static function studyGroupToStudyGroupViewModel(StudyGroup $studyGroup): StudyGroupViewModel
    {
        return new StudyGroupViewModel(
            id: $studyGroup->getId(),
            number: $studyGroup->getNumber(),
            name: $studyGroup->getName(),
            specialization: $studyGroup->getSpecialization(),
            classMaster: UserMapper::userToUserViewModel($studyGroup->getClassMaster())
        );
    }

    /**
     * @param StudyGroup[] $studyGroups
     * @return StudyGroupViewModel[]
     */
    public static function studyGroupsToStudyGroupViewModels(array $studyGroups): array
    {
        return \array_map(
            fn (StudyGroup $studyGroup) => self::studyGroupToStudyGroupViewModel($studyGroup),
            $studyGroups
        );
    }
}