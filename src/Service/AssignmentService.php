<?php

namespace App\Service;

use App\Dto\Dto\AssignmentDto;
use App\Dto\Dto\AssignmentResponseDto;
use App\Dto\Dto\NlpInput;
use App\Dto\Mapper\AssignmentMapper;
use App\Dto\Mapper\AssignmentResponseMapper;
use App\Dto\Mapper\NlpInputMapper;
use App\Dto\ViewModel\AssignmentResponseViewModel;
use App\Dto\ViewModel\AssignmentViewModel;
use App\Entity\Assignment;
use App\Entity\AssignmentResponse;
use App\Repository\AssignmentRepository;
use App\Repository\AssignmentResponseRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Validator\AssignmentValidator;

class AssignmentService
{
    public function __construct(
        private AssignmentRepository $assignmentRepository,
        private UserRepository $userRepository,
        private SubjectRepository $subjectRepository,
        private AssignmentResponseRepository $assignmentResponseRepository,
        private FileService $fileService,
        private AssignmentValidator $assignmentValidator,
        private string $assignmentsDirPath,
        private string $responsesDirPath,
    ) {
    }

    /**
     * @return AssignmentViewModel[]
     */
    public function getAssignmentsByUser(int $userId): array
    {
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new ServiceException('User not found!');
        }

        if (true === AccessValidator::hasTeacherAccess($user->getRoles())) {
            return \array_map(
                fn (Assignment $assignment)
                    => AssignmentMapper::assignmentToAssignmentViewModel($assignment)
                        ->setResponses(
                            AssignmentResponseMapper::assignmentResponsesToAssignmentResponseViewModels($assignment->getAssignmentResponses()->toArray())
                        ),
                $this->assignmentRepository->findByTeacher($userId)
            );
        }

        return \array_map(
            fn (Assignment $assignment)
                => AssignmentMapper::assignmentToAssignmentViewModel($assignment)
                    ->setResponses(\array_values(
                        AssignmentResponseMapper::assignmentResponsesToAssignmentResponseViewModels(
                            \array_filter(
                                $assignment->getAssignmentResponses()->toArray(),
                                fn (AssignmentResponse $assignmentResponse) => $assignmentResponse->getStudent()->getId() === $userId
                            )
                        )
                    )),
            $this->assignmentRepository->findByStudyGroup($user->getStudyGroup()->getId())
        );
    }

    public function addAssignment(AssignmentDto $assignmentDto): AssignmentViewModel
    {
        $this->assignmentValidator->validate($assignmentDto);

        $subject = $this->subjectRepository->find($assignmentDto->getSubjectId());
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        $assignment = AssignmentMapper::assignmentDtoToAssignment($assignmentDto)
            ->setSubject($subject)
            ->setRequirementFilePath(null)
            ->setClosed(false);
        $this->assignmentRepository->add($assignment);

        return AssignmentMapper::assignmentToAssignmentViewModel($assignment);
    }

    public function updateAssignmentAttachmentPath(int $assignmentId, string $path): int
    {
        $rowsUpdated = $this->assignmentRepository->updateAssignmentPath($assignmentId, $path);

        if (0 === $rowsUpdated) {
            throw new ServiceException('Assignment does not exist!');
        }

        return $rowsUpdated;
    }

    public function updateAssignment(int $id, AssignmentDto $assignmentDto): AssignmentViewModel
    {
        $this->assignmentValidator->validate($assignmentDto);

        $assignment = $this->assignmentRepository->find($id);
        if (null === $assignment) {
            throw new ServiceException('Assignment does not exist!');
        }

        $editedAssignment = $this->updateAssignmentWithAssignmentDtoFeatures($assignment, $assignmentDto);
        $this->assignmentRepository->update($editedAssignment);

        return AssignmentMapper::assignmentToAssignmentViewModel($editedAssignment);
    }

    public function removeAssignment(int $id): AssignmentViewModel
    {
        $assignment = $this->assignmentRepository->find($id);

        if (null === $assignment) {
            throw new ServiceException('Assignment does not exist!');
        }

        if (null !== $assignment->getRequirementFilePath()) {
            $this->fileService->removeFile($this->assignmentsDirPath . '/' . $assignment->getRequirementFilePath());
        }

        $this->assignmentRepository->remove($assignment);

        return AssignmentMapper::assignmentToAssignmentViewModel($assignment->setId($id));
    }

    public function updateAssignmentClosed(int $id, bool $closed): int
    {
        $updatedRows = $this->assignmentRepository->updateAssignmentClosed($id, $closed);

        if (0 === $updatedRows) {
            throw new ServiceException('Assignment does not exist!');
        }

        return $updatedRows;
    }

    public function addAssignmentResponse(AssignmentResponseDto $assignmentResponseDto): AssignmentResponseViewModel
    {
        $assignment = $this->assignmentRepository->find($assignmentResponseDto->getAssignmentId());
        if (null === $assignment) {
            throw new ServiceException('Assignment does not exist!');
        }
        if ($assignment->isClosed()) {
            throw new ServiceException('Assignment is closed!');
        }

        $student = $this->userRepository->find($assignmentResponseDto->getStudentId());
        if (null === $student) {
            throw new ServiceException('Student does not exist!');
        }

        $assignmentResponse = AssignmentResponseMapper::assignmentResponseDtoToAssignmentResponse($assignmentResponseDto)
            ->setAssignment($assignment)
            ->setStudent($student)
            ->setFilePath(null);

        $this->assignmentResponseRepository->add($assignmentResponse);

        return AssignmentResponseMapper::assignmentResponseToAssignmentResponseViewModel($assignmentResponse);
    }

    public function updateAssignmentResponseAttachmentPath(int $assignmentResponseId, string $path): int
    {
        $rowsUpdated = $this->assignmentResponseRepository->updateAssignmentResponsePath($assignmentResponseId, $path);

        if (0 === $rowsUpdated) {
            throw new ServiceException('Assignment does not exist!');
        }

        return $rowsUpdated;
    }

    public function removeAssignmentResponse(int $id): AssignmentResponseViewModel
    {
        $assignmentResponse = $this->assignmentResponseRepository->find($id);

        if (null === $assignmentResponse) {
            throw new ServiceException('Assignment response does not exist!');
        }

        $this->fileService->removeFile($this->responsesDirPath . '/' . $assignmentResponse->getFilePath());


        $this->assignmentResponseRepository->remove($assignmentResponse);

        return AssignmentResponseMapper::assignmentResponseToAssignmentResponseViewModel($assignmentResponse->setId($id));
    }

    /**
     * @return NlpInput[]
     */
    public function getTeacherNlpInputsByAssignment(int $assignmentId): array
    {
        return NlpInputMapper::assignmentResponsesToNlpInputs(
            $this->assignmentResponseRepository->findByAssignmentId($assignmentId)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getStudentNlpInputsByAssignment(int $assignmentId, int $studentId): array
    {
        $assignmentResponses = $this->assignmentResponseRepository->findByAssignmentId($assignmentId);

        $studentAssignmentResponse = null;

        foreach ($assignmentResponses as $key => $response) {
            if ($response->getStudent()->getId() === $studentId) {
                $studentAssignmentResponse = $response;

                \array_splice($assignmentResponses, $key, 1);
                break;
            }
        }

        if (null === $studentAssignmentResponse) {
            throw new ServiceException('Invalid assignment response provided!');
        }

        return [
            'studentInput' => NlpInputMapper::assignmentResponseToNlpInput($studentAssignmentResponse),
            'otherInputs' => NlpInputMapper::assignmentResponsesToNlpInputs($assignmentResponses),
        ];
    }

    private function updateAssignmentWithAssignmentDtoFeatures(Assignment $assignment, AssignmentDto $assignmentDto): Assignment
    {
        return $assignment
            ->setTitle($assignmentDto->getTitle())
            ->setDescription($assignmentDto->getDescription())
            ->setDueTo((new \DateTime())->setTimestamp($assignmentDto->getDueTo()));
    }
}