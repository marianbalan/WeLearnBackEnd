<?php

namespace App\Service;

use App\Dto\Dto\SchoolDto;
use App\Dto\Dto\UserDto;
use App\Dto\Mapper\SchoolMapper;
use App\Dto\Mapper\StudyGroupMapper;
use App\Dto\Mapper\UserMapper;
use App\Dto\ViewModel\UserViewModel;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Repository\SchoolRepository;
use App\Repository\StudyGroupRepository;
use App\Repository\UserRepository;
use App\Service\Exception\ServiceException;
use App\Utils\AccessValidator;
use App\Utils\UserRole;
use App\Validator\SchoolValidator;
use App\Validator\UserValidator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserService
{
    private const TOKEN_TIME = 8 * 60 * 60 * 1000; // 8 hours

    public function __construct(
        private UserRepository      $userRepository,
        private SchoolRepository    $schoolRepository,
        private StudyGroupRepository $studyGroupRepository,
        private TokenManagerService $jwtManagerService,
        private MailService $mailService,
        private UserValidator $userValidator,
        private SchoolValidator $schoolValidator,
    ) {
    }

    /**
     * @throws ServiceException
     */
    public function register(UserDto $userDto, SchoolDto $schoolDto): UserViewModel
    {
        $this->userValidator->validate($userDto);
        $this->schoolValidator->validate($schoolDto);

        $existingUser = $this->userRepository->findByEmail($userDto->getEmail());

        if (null !== $existingUser) {
            throw new ServiceException("User already exists!");
        }

        $existingSchool = $this->schoolRepository->findByCui($schoolDto->getCui());

        if (null !== $existingSchool) {
            throw new ServiceException("School already exists!");
        }

        $school = SchoolMapper::schoolDtoToSchool($schoolDto);
        $user = UserMapper::userDtoToUser($userDto)
            ->setPassword(\hash('sha256', $userDto->getPassword()))
            ->addRole(UserRole::USER)
            ->addRole(UserRole::TEACHER)
            ->addRole(UserRole::DIRECTOR)
            ->setActivated(false)
            ->setSchool($school);
        $user->setActivationToken($this->jwtManagerService->generateConfirmationToken($user));

        $this->schoolRepository->add($school);
        $this->userRepository->add($user);

        $this->mailService->sendMail(
            $user->getEmail(),
            $this->mailService->buildRegistrationConfirmationTemplate($user)
        );

        return UserMapper::userToUserViewModel($user);
    }

    /**
     * @throws ServiceException
     */
    public function login(string $email, string $password): string
    {
        $existingUser = $this->userRepository->findByEmail($email);

        if (null === $existingUser || \hash('sha256', $password) !== $existingUser->getPassword()) {
            throw new ServiceException("Invalid username or password!");
        }

        if (false === $existingUser->getActivated()) {
            throw new ServiceException('You need to activate your account first!');
        }

        return $this->jwtManagerService->generateAuthToken($existingUser, \time() + self::TOKEN_TIME);
    }

    /**
     * @throws ServiceException
     */
    public function addUser(UserDto $userDto, User $reporter): UserViewModel
    {
        $this->userValidator->validate($userDto);

        if (
            [UserRole::STUDENT] !== $userDto->getRoles() &&
            false === AccessValidator::hasDirectorAccess($reporter->getRoles())
        ) {
            throw new UnauthorizedHttpException('','Access denied!');
        }

        if (null !== $this->userRepository->findByEmail($userDto->getEmail())) {
            throw new ServiceException("User already exists!");
        }

        $studyGroup = null;
        if (null !== $userDto->getStudyGroupId()) {
            $studyGroup = $this->studyGroupRepository->find($userDto->getStudyGroupId());
        }

        $user = UserMapper::userDtoToUser($userDto)
            ->addRole(UserRole::USER)
            ->addRole($userDto->getRoles()[0])
            ->setStudyGroup($studyGroup)
            ->setActivated(false)
            ->setSchool($reporter->getSchool());

        $user->setActivationToken($this->jwtManagerService->generateConfirmationToken($user));

        $this->userRepository->add($user);
        $this->mailService->sendMail(
            $user->getEmail(),
            $this->mailService->buildSetPasswordTemplate($user)
        );

        $userViewModel = UserMapper::userToUserViewModel($user);
        if (\in_array(UserRole::STUDENT, $user->getRoles(), true)) {
            $userViewModel->setStudyGroup(StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroup));
        }

        return $userViewModel;
    }

    /**
     * @throws ServiceException
     */
    public function updateUser(int $id, UserDto $userDto, User $reporter): UserViewModel
    {
        if (
            [UserRole::STUDENT] !== $userDto->getRoles() &&
            false === AccessValidator::hasDirectorAccess($reporter->getRoles())
        ) {
            throw new UnauthorizedHttpException('','Access denied!');
        }

        $this->userValidator->validate($userDto);

        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new ServiceException("User does not exists!");
        }

        if ($user->getSchool()->getId() !== $reporter->getSchool()->getId()) {
            throw new UnauthorizedHttpException('', 'Access denied.');
        }
        if (
            true === AccessValidator::hasClassMasterAccess($reporter->getRoles()) &&
            $user->getStudyGroup()->getId() !== $reporter->getStudyGroup()->getId()
        ) {
            throw new UnauthorizedHttpException('','Access denied!');
        }


        $user = $this->updateUserWithUserDtoFeatures($user, $userDto);

        $this->userRepository->update($user);

        $model = UserMapper::userToUserViewModel($user);
        if (null !== $user->getStudyGroup()) {
            $model->setStudyGroup(StudyGroupMapper::studyGroupToStudyGroupViewModel($user->getStudyGroup()));
        }

        return $model;
    }

    public function getUser(int $id): User
    {
        $user = $this->userRepository->find($id);

        if (null === $user) {
            throw new ServiceException("User does not exist!");
        }

        return $user;
    }

    public function getUserAsViewModel(int $id): UserViewModel
    {
        return UserMapper::userToUserViewModel($this->getUser($id));
    }

    /**
     * @return UserViewModel[]
     */
    public function getNonClassMasterTeachersBySchoolId(int $schoolId): array
    {
        return UserMapper::userViewModelsFromDbResults(
            $this->userRepository->findNonClassMasterTeachersBySchoolId($schoolId)
        );
    }

    /**
     * @return UserViewModel[]
     */
    public function getBySchoolAndRole(string $role, int $schoolId): array
    {
        $nonHydratedUsers = $this->userRepository->findBySchoolAndRole($schoolId, $role);

        if (UserRole::STUDENT !== $role) {
            return UserMapper::userViewModelsFromDbResults($nonHydratedUsers);
        }

        $studyGroups = $this->extractStudyGroupFromUsers($nonHydratedUsers);

        return \array_map(
            fn (array $user) => UserMapper::userViewModelFromDbResult($user)
                ->setStudyGroup(
                    StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroups[$user['study_group_id']])
                ),
            $nonHydratedUsers
        );
    }

    /**
     * @return UserViewModel[]
     */
    public function getByStudyGroupAndRole(string $role, int $studyGroupId): array
    {
        $nonHydratedUsers = $this->userRepository->findByStudyGroupAndRole($studyGroupId, $role);

        if (UserRole::STUDENT !== $role) {
            return UserMapper::userViewModelsFromDbResults($nonHydratedUsers);
        }

        $studyGroups = $this->extractStudyGroupFromUsers($nonHydratedUsers);

        return \array_map(
            fn (array $user) => UserMapper::userViewModelFromDbResult($user)
                ->setStudyGroup(
                    StudyGroupMapper::studyGroupToStudyGroupViewModel($studyGroups[$user['study_group_id']])
                ),
            $nonHydratedUsers
        );
    }

    public function removeUser(int $id, User $reporter): UserViewModel
    {
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new ServiceException('User does not exist!');
        }

        if (false === $user->getTeachingSubjects()->isEmpty()) {
            throw new ServiceException('Please update the teaching subjects first!');
        }

        if ($user->getSchool()->getId() !== $reporter->getSchool()->getId()) {
            throw new UnauthorizedHttpException('', 'Access denied.');
        }
        if (
            true === AccessValidator::hasClassMasterAccess($reporter->getRoles()) &&
            $user->getStudyGroup()->getId() !== $reporter->getStudyGroup()->getId()
        ) {
            throw new UnauthorizedHttpException('','Access denied!');
        }

        $this->userRepository->remove($user);

        return UserMapper::userToUserViewModel($user->setId($id));
    }

    public function activateAccount(string $token): UserViewModel
    {
        $user = $this->userRepository->findByActivationToken($token);

        if (null === $user) {
            throw new ServiceException('User does not exist!');
        }
        if (true === $user->getActivated()) {
            throw new ServiceException('This account is already activated');
        }

        $user->setActivated(true);
        $user->setActivationToken(null);

        $this->userRepository->update($user);

        return UserMapper::userToUserViewModel($user);
    }

    public function setPassword(string $token, string $password): UserViewModel
    {
        $user = $this->userRepository->findByActivationToken($token);

        if (null === $user) {
            throw new ServiceException('User does not exist!');
        }
        if (true === $user->getActivated()) {
            throw new ServiceException('This account is already activated');
        }

        $user->setPassword(\hash('sha256', $password))
            ->setActivated(true)
            ->setActivationToken(null);

        $this->userRepository->update($user);

        return UserMapper::userToUserViewModel($user);
    }

    private function updateUserWithUserDtoFeatures(User $user, UserDto $userDto): User
    {
        $studyGroup = null;
        if (null !== $userDto->getStudyGroupId()) {
            $studyGroup = $this->studyGroupRepository->find($userDto->getStudyGroupId());
        }

        return $user
            ->setEmail($userDto->getEmail())
            ->setFirstName($userDto->getFirstName())
            ->setLastName($userDto->getLastName())
            ->setPin($userDto->getPin())
            ->setPhoneNumber($userDto->getPhoneNumber())
            ->setStudyGroup($studyGroup);
    }

    /**
     * @param array<array<string, mixed>> $nonHydratedUsers
     * @return array<int, StudyGroup>
     */
    private function extractStudyGroupFromUsers(array $nonHydratedUsers): array
    {
        $studyGroups = $this->studyGroupRepository->findBy(
            ['id' => \array_unique(\array_map(
                fn (array $user) => $user['study_group_id'],
                $nonHydratedUsers
            ))]
        );

        return \array_combine(
            \array_map(
                fn (StudyGroup $studyGroup) => $studyGroup->getId(),
                $studyGroups
            ),
            $studyGroups
        );
    }
}
