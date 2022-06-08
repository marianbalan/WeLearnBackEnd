<?php

namespace App\Service;

use App\Dto\Dto\SubjectDto;
use App\Dto\Mapper\SubjectMapper;
use App\Dto\ViewModel\SubjectViewModel;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\StudyGroupRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Utils\UserRole;
use App\Validator\SubjectValidator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SubjectService
{
    public function __construct(
        private StudyGroupRepository $studyGroupRepository,
        private UserRepository $userRepository,
        private SubjectRepository $subjectRepository,
        private SubjectValidator $subjectValidator,
    ) {
    }

    public function addSubject(SubjectDto $subjectDto): SubjectViewModel
    {
        $studyGroup = $this->studyGroupRepository->find($subjectDto->getStudyGroupId());
        if (null === $studyGroup) {
            throw new ServiceException('Study group does not exist!');
        }

        $teacher = $this->userRepository->find($subjectDto->getTeacherId());
        if (null === $teacher || false === \in_array(UserRole::TEACHER, $teacher->getRoles(), true)) {
            throw new ServiceException('Teacher does not exist!');
        }

        $subject = SubjectMapper::subjectDtoToSubject($subjectDto)
            ->setStudyGroup($studyGroup)
            ->setTeacher($teacher);

        $this->subjectValidator->validate($subject);

        $this->subjectRepository->add($subject);

        return SubjectMapper::subjectToSubjectViewModel($subject);
    }

    /**
     * @return SubjectViewModel[]
     */
    public function getByTeacher(int $teacherId): array
    {
        return SubjectMapper::subjectsToSubjectViewModels(
            $this->subjectRepository->findByTeacher($teacherId)
        );
    }

    /**
     * @return SubjectViewModel[]
     */
    public function getByStudyGroup(int $studyGroupId): array
    {
        return SubjectMapper::subjectsToSubjectViewModels(
            $this->subjectRepository->findByStudyGroup($studyGroupId)
        );
    }

    /**
     * @return SubjectViewModel[]
     */
    public function getBySchool(int $schoolId): array
    {
        return SubjectMapper::subjectsToSubjectViewModels(
            $this->subjectRepository->findBySchoolId($schoolId)
        );
    }


    public function removeSubject(int $subjectId): SubjectViewModel
    {
        $subject = $this->subjectRepository->find($subjectId);
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        $this->subjectRepository->remove($subject);

        return SubjectMapper::subjectToSubjectViewModel($subject->setId($subjectId));
    }

    public function updateSubject(int $id, SubjectDto $subjectDto): SubjectViewModel
    {
        $subject = $this->subjectRepository->find($id);
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        $subject = $this->updateSubjectWithSubjectDtoFeatures($subject, $subjectDto);

        $this->subjectValidator->validate($subject);
        $this->subjectRepository->update($subject);

        return SubjectMapper::subjectToSubjectViewModel($subject);
    }

    public function getSubject(int $id): SubjectViewModel
    {
        $subject = $this->subjectRepository->find($id);
        if (null === $subject) {
            throw new ServiceException('Subject does not exist!');
        }

        return SubjectMapper::subjectToSubjectViewModel($subject);
    }

    private function updateSubjectWithSubjectDtoFeatures(Subject $subject, SubjectDto $subjectDto): Subject
    {
        $subject->setName($subjectDto->getName());

        if ($subject->getTeacher()->getId() !== $subjectDto->getTeacherId()) {
            $teacher = $this->userRepository->find($subjectDto->getTeacherId());
            if (null === $teacher) {
                throw new ServiceException('Teacher does not exist!');
            }

            $subject->setTeacher($teacher);
        }

        if ($subject->getStudyGroup()->getId() !== $subjectDto->getStudyGroupId()) {
            $studyGroup = $this->studyGroupRepository->find($subjectDto->getStudyGroupId());
            if (null === $studyGroup) {
                throw new ServiceException('Study group does not exist!');
            }

            $subject->setStudyGroup($studyGroup);
        }

        return $subject;
    }

}