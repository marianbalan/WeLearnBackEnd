<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\AssignmentResponseDto;
use App\Dto\ViewModel\AssignmentResponseViewModel;
use App\Entity\AssignmentResponse;

class AssignmentResponseMapper
{
    public static function assignmentResponseDtoToAssignmentResponse(
        AssignmentResponseDto $assignmentResponseDto
    ): AssignmentResponse {
        return new AssignmentResponse(
            date: (new \DateTime())->setTimestamp($assignmentResponseDto->getDate())
        );
    }

    public static function assignmentResponseToAssignmentResponseViewModel(
        AssignmentResponse $assignmentResponse
    ): AssignmentResponseViewModel {
        return new AssignmentResponseViewModel(
            id: $assignmentResponse->getId(),
            date: $assignmentResponse->getDate()->getTimestamp(),
            assignmentId: $assignmentResponse->getAssignment()->getId(),
            student: UserMapper::userToUserViewModel(
                $assignmentResponse->getStudent()
            ),
            filePath: $assignmentResponse->getFilePath()
        );
    }

    /**
     * @param AssignmentResponse[] $assignmentResponses
     * @return AssignmentResponseViewModel[]
     */
    public static function assignmentResponsesToAssignmentResponseViewModels(
        array $assignmentResponses
    ): array {
        return \array_map(
            fn (AssignmentResponse $assignmentResponse)
                => self::assignmentResponseToAssignmentResponseViewModel($assignmentResponse),
            $assignmentResponses
        );
    }
}