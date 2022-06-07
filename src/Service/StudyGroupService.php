<?php

namespace App\Service;

use App\Dto\Dto\StudyGroupDto;
use App\Dto\Mapper\StudyGroupMapper;
use App\Dto\ViewModel\StudyGroupViewModel;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Repository\SchoolRepository;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Utils\UserRole;
use App\Validator\StudyGroupValidator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class StudyGroupService
{
    public function __construct(
        private UserRepository       $userRepository,
        private StudyGroupRepository $studyGroupRepository,
        private SchoolRepository     $schoolRepository,
        private StudyGroupValidator $studyGroupValidator,
    ) {
    }

    public function addStudyGroup(StudyGroupDto $studyGroupDto): StudyGroupViewModel
    {
        $this->studyGroupValidator->validate($studyGroupDto);

        $school = $this->schoolRepository->find($studyGroupDto->getSchoolId());
        if (null === $school) {
            throw new ServiceException('School does not exist!');
        }

        $classMaster = $this->userRepository->find($studyGroupDto->getClassMasterId());
        if (null === $classMaster) {
            throw new ServiceException('Teacher does not exist!');
        }

        $studyGroup = StudyGroupMapper::studyGroupDtoToStudyGroup($studyGroupDto)
            ->setSchool($school)
            ->setClassMaster($classMaster);
        $this->studyGroupRepository->add($studyGroup);

        $classMaster->addRole(UserRole::CLASS_MASTER)->setStudyGroup($studyGroup);
        $this->userRepository->update($classMaster);

        return StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroup);
    }

    public function updateStudyGroup(int $id, StudyGroupDto $studyGroupDto): StudyGroupViewModel
    {
        $this->studyGroupValidator->validate($studyGroupDto);

        $studyGroup = $this->studyGroupRepository->find($id);
        if (null === $studyGroup) {
            throw new ServiceException('Study group does not exist!');
        }

        $studyGroup = $this->updateStudyGroupWithStudyGroupDtoFeatures($studyGroup, $studyGroupDto);
        $this->studyGroupRepository->update($studyGroup);

        return StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroup);
    }

    public function removeStudyGroup(int $id): StudyGroupViewModel
    {
        $studyGroup = $this->studyGroupRepository->find($id);
        if (null === $studyGroup) {
            throw new ServiceException('Study group does not exist!');
        }

        if (1 > $studyGroup->getStudents()->count() ||
            false === $studyGroup->getSubjects()->isEmpty()) {
            throw new ServiceException('Please update the students and subjects first!');
        }

        if (null !== $studyGroup->getClassMaster()) {
            $studyGroup->getClassMaster()->setStudyGroup(null);
        }
        $this->userRepository->update($studyGroup->getClassMaster());
        $this->studyGroupRepository->remove($studyGroup);

        return StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroup->setId($id));
    }

    /**
     * @return StudyGroupViewModel[]
     */
    public function getStudyGroupsBySchoolId(int $schoolId): array
    {
        return StudyGroupMapper::studyGroupsToStudyGroupViewModels(
            $this->studyGroupRepository->getBySchoolId($schoolId)
        );
    }

    private function updateStudyGroupWithStudyGroupDtoFeatures(StudyGroup $studyGroup, StudyGroupDto $studyGroupDto): StudyGroup
    {
        $newStudyGroup = $studyGroup
            ->setName($studyGroupDto->getName())
            ->setNumber($studyGroupDto->getNumber())
            ->setSpecialization($studyGroupDto->getSpecialization());

        if ($studyGroup->getClassMaster()->getId() !== $studyGroupDto->getClassMasterId()) {
            $newClassMaster = $this->userRepository->find($studyGroupDto->getClassMasterId());
            if (null === $newClassMaster) {
                throw new ServiceException('Class master does not exist!');
            }

            $oldClassMaster = $studyGroup->getClassMaster();
            $oldClassMaster->removeRole(UserRole::CLASS_MASTER)->setStudyGroup(null);

            $newStudyGroup->setClassMaster($newClassMaster);
            $newClassMaster->addRole(UserRole::CLASS_MASTER)->setStudyGroup($newStudyGroup);

            $this->userRepository->update($oldClassMaster);
            $this->userRepository->update($newClassMaster);
        }

        return $newStudyGroup;
    }
}