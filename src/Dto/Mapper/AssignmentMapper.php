<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\AssignmentDto;
use App\Dto\ViewModel\AssignmentViewModel;
use App\Entity\Assignment;

class AssignmentMapper
{
    public static function assignmentToAssignmentViewModel(Assignment $assignment): AssignmentViewModel
    {
        return new AssignmentViewModel(
            id: $assignment->getId(),
            title: $assignment->getTitle(),
            description: $assignment->getDescription(),
            date: $assignment->getDate()->getTimestamp(),
            dueTo: $assignment->getDueTo()->getTimestamp(),
            subject: SubjectMapper::subjectToSubjectViewModel($assignment->getSubject()),
            requirementFilePath: $assignment->getRequirementFilePath(),
            closed: $assignment->isClosed(),
        );
    }

    /**
     * @param Assignment[] $assignments
     * @return AssignmentViewModel[]
     */
    public static function assignmentsToAssignmentViewModels(array $assignments): array
    {
        return \array_map(
            fn (Assignment $assignment) => self::assignmentToAssignmentViewModel($assignment),
            $assignments
        );
    }

    public static function assignmentDtoToAssignment(AssignmentDto $assignmentDto): Assignment
    {
        return new Assignment(
            title: $assignmentDto->getTitle(),
            description: $assignmentDto->getDescription(),
            date: (new \DateTime())->setTimestamp($assignmentDto->getDate()),
            dueTo: (new \DateTime())->setTimestamp($assignmentDto->getDueTo()),
        );
    }
}