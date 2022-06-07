<?php

namespace App\Service;

use App\Dto\Dto\NonAttendanceDto;
use App\Dto\Mapper\NonAttendanceMapper;
use App\Dto\ViewModel\NonAttendanceViewModel;
use App\Entity\User;
use App\Repository\NonAttendanceRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class NonAttendanceService
{
    public function __construct(
        private NonAttendanceRepository     $nonAttendanceRepository,
        private UserRepository    $userRepository,
        private SubjectRepository $subjectRepository,
    ) {
    }

    public function addNonAttendance(NonAttendanceDto $nonAttendanceDto): NonAttendanceViewModel
    {
        $student = $this->userRepository->find($nonAttendanceDto->getStudentId());
        if (null === $student) {
            throw new ServiceException('Student does not exist!');
        }

        $subject = $this->subjectRepository->find($nonAttendanceDto->getSubjectId());
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        $nonAttendance = NonAttendanceMapper::nonAttendanceDtoToNonAttendance($nonAttendanceDto)
            ->setStudent($student)
            ->setSubject($subject);

        $this->nonAttendanceRepository->add($nonAttendance);

        return NonAttendanceMapper::nonAttendanceToNonAttendanceViewModel($nonAttendance);
    }

    /**
     * @return NonAttendanceViewModel[]
     */
    public function getNonAttendancesByStudentIdAndSubjectId(int $studentId, int $subjectId): array
    {
        return NonAttendanceMapper::nonAttendancesToNonAttendanceViewModels(
            $this->nonAttendanceRepository->findByUserIdAndSubjectId($studentId, $subjectId)
        );
    }

    public function updateNonAttendanceMotivation(int $id, bool $isMotivated): int
    {
        $updatedRows = $this->nonAttendanceRepository->updateMotivated($id, $isMotivated);

        if (0 === $updatedRows) {
            throw new ServiceException('Non attendance not found!');
        }

        return $updatedRows;
    }
}