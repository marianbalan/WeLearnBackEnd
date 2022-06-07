<?php

namespace App\Service;

use App\Dto\Mapper\GradeMapper;
use App\Dto\Mapper\NonAttendanceMapper;
use App\Dto\Mapper\SubjectMapper;
use App\Dto\Mapper\UserMapper;
use App\Dto\ViewModel\GradeViewModel;
use App\Dto\ViewModel\StudentSituationViewModel;
use App\Dto\ViewModel\SubjectSituationViewModel;
use App\Entity\Grade;
use App\Entity\NonAttendance;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\GradeRepository;
use App\Repository\NonAttendanceRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Utils\UserRole;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SituationService
{
    public function __construct(
        private UserRepository $userRepository,
        private GradeRepository $gradeRepository,
        private NonAttendanceRepository $nonAttendanceRepository,
        private SubjectRepository $subjectRepository,
    ) {
    }

    /**
     * @return StudentSituationViewModel[]
     */
    public function getStudentSituationByStudent(int $studentId): array
    {
        $student = $this->userRepository->find($studentId);
        if (null === $student) {
            throw new ServiceException('User does not exist!');
        }
        if (null === $student->getStudyGroup()) {
            throw new ServiceException('This user is not assigned to any study-group!');
        }

        return $this->computeTotalStudentSituation(
            $this->subjectRepository->findByStudyGroup($student->getStudyGroup()->getId()),
            $this->gradeRepository->findByUserId($studentId),
            $this->nonAttendanceRepository->findByUserId($studentId)
        );
    }

    /**
     * @return SubjectSituationViewModel[]
     */
    public function getSubjectSituationBySubject(int $subjectId): array
    {
        $subject = $this->subjectRepository->find($subjectId);
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        return $this->computeTotalSubjectSituation(
            $this->userRepository->findByStudyGroupAndRole($subject->getStudyGroup()->getId(), UserRole::STUDENT),
            $this->gradeRepository->findBySubjectId($subjectId),
            $this->nonAttendanceRepository->findBySubjectId($subjectId)
        );
    }

    /**
     * @param array<array<string,mixed>> $users
     * @param Grade[] $grades
     * @param NonAttendance[] $nonAttendances
     * @return SubjectSituationViewModel[]
     */
    private function computeTotalSubjectSituation(array $users, array $grades, array $nonAttendances): array
    {
        /** @var SubjectSituationViewModel[] $situation */
        $situation = [];

        foreach ($users as $user) {
            $filteredGrades = \array_map(
                fn (Grade $grade) => GradeMapper::gradeToGradeViewModel($grade),
                \array_values(\array_filter(
                    $grades,
                    fn (Grade $grade) => $grade->getStudent()->getId() === $user['id']
                ))
            );
            $gradesLength = \count($filteredGrades);

            $situation[] = new SubjectSituationViewModel(
                user: UserMapper::userViewModelFromDbResult($user),
                grades: $filteredGrades,
                nonAttendances: \array_map(
                    fn (NonAttendance $nonAttendance) =>
                    NonAttendanceMapper::nonAttendanceToNonAttendanceViewModel($nonAttendance),
                    \array_values(\array_filter(
                        $nonAttendances,
                        fn (NonAttendance $nonAttendance) => $nonAttendance->getStudent()->getId() === $user['id']
                    ))
                ),
                averageScore: 0 < $gradesLength ? (int) \round(\array_sum(\array_map(
                        fn (GradeViewModel $grade) => $grade->getGrade(),
                        $filteredGrades
                    )) / $gradesLength) : null,
            );
        }

        return $situation;
    }

    /**
     * @param Subject[] $subjects
     * @param Grade[] $grades
     * @param NonAttendance[] $nonAttendances
     * @return StudentSituationViewModel[]
     */
    private function computeTotalStudentSituation(array $subjects, array $grades, array $nonAttendances): array
    {
        /** @var StudentSituationViewModel[] $situation */
        $situation = [];

        foreach ($subjects as $subject) {
            $filteredGrades = \array_map(
                fn (Grade $grade) => GradeMapper::gradeToGradeViewModel($grade),
                \array_values(\array_filter(
                    $grades,
                    fn (Grade $grade) => $grade->getSubject() === $subject
                ))
            );
            $gradesLength = \count($filteredGrades);

            $situation[] = new StudentSituationViewModel(
                subject: SubjectMapper::subjectToSubjectViewModel($subject),
                grades: $filteredGrades,
                nonAttendances: \array_map(
                    fn (NonAttendance $nonAttendance) =>
                    NonAttendanceMapper::nonAttendanceToNonAttendanceViewModel($nonAttendance),
                    \array_values(\array_filter(
                        $nonAttendances,
                        fn (NonAttendance $nonAttendance) => $nonAttendance->getSubject() === $subject
                    ))
                ),
                averageScore: 0 < $gradesLength ? (int) \round(\array_sum(\array_map(
                    fn (GradeViewModel $grade) => $grade->getGrade(),
                    $filteredGrades
                )) / $gradesLength) : null,
            );
        }

        return $situation;
    }
}