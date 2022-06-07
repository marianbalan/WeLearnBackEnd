<?php

namespace App\Service;

use _PHPStan_ae8980142\Nette\Neon\Token;
use App\Dto\Dto\GradeDto;
use App\Dto\Mapper\GradeMapper;
use App\Dto\ViewModel\GradeViewModel;
use App\Entity\User;
use App\Repository\GradeRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Validator\GradeValidator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GradeService
{
    public function __construct(
        private GradeRepository   $gradeRepository,
        private UserRepository    $userRepository,
        private SubjectRepository $subjectRepository,
        private GradeValidator $gradeValidator,
    ) {
    }

    public function addGrade(GradeDto $gradeDto): GradeViewModel
    {
        $this->gradeValidator->validate($gradeDto);

        $student = $this->userRepository->find($gradeDto->getStudentId());
        if (null === $student) {
            throw new ServiceException('Student does not exist!');
        }

        $subject = $this->subjectRepository->find($gradeDto->getSubjectId());
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        $grade = GradeMapper::gradeDtoToGrade($gradeDto)
            ->setStudent($student)
            ->setSubject($subject);

        $this->gradeRepository->add($grade);

        return GradeMapper::gradeToGradeViewModel($grade);
    }

    /**
     * @return GradeViewModel[]
     */
    public function getGradesByStudentIdAndSubjectId(int $studentId, int $subjectId): array
    {
        return GradeMapper::gradesToGradeViewModels(
            $this->gradeRepository->findByUserIdAndSubjectId($studentId, $subjectId)
        );
    }

    /**
     * @return GradeViewModel[]
     */
    public function getGradesByStudentId(int $studentId): array
    {
        return GradeMapper::gradesToGradeViewModels(
            $this->gradeRepository->findByUserId($studentId)
        );
    }

    public function removeGrade(int $gradeId): GradeViewModel
    {
        $grade = $this->gradeRepository->find($gradeId);
        if (null === $grade) {
            throw new ServiceException('Grade does not exist!');
        }

        $this->gradeRepository->remove($grade);

        return GradeMapper::gradeToGradeViewModel($grade->setId($gradeId));
    }

    public function editGradeMark(int $id, int $grade): int
    {
        $this->gradeValidator->validateGradeMark($grade);

        $updatedRows = $this->gradeRepository->updateGradeMark($id, $grade);
        if (0 === $updatedRows) {
            throw new ServiceException('Grade does not exist!');
        }

        return $updatedRows;
    }
}