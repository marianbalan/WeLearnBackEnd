<?php

namespace App\Dto\Mapper;

use App\Dto\Dto\UserDto;
use App\Dto\ViewModel\UserViewModel;
use App\Entity\User;

class UserMapper
{
    public static function userToUserDto(User $user): UserDto
    {
        return new UserDto(
            id: $user->getId(),
            email: $user->getEmail(),
            password: $user->getPassword(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            pin: $user->getPin(),
            phoneNumber: $user->getPhoneNumber(),
            roles: $user->getRoles(),
        );
    }

    public static function userDtoToUser(UserDto $userDto): User
    {
        return new User(
            email: $userDto->getEmail(),
            roles: $userDto->getRoles(),
            password: $userDto->getPassword() ?? '',
            firstName: $userDto->getFirstName(),
            lastName: $userDto->getLastName(),
            pin: $userDto->getPin(),
            phoneNumber: $userDto->getPhoneNumber(),
        );
    }

    public static function userToUserViewModel(User $user): UserViewModel
    {
        return new UserViewModel(
            id: $user->getId(),
            email: $user->getEmail(),
            firstName: $user->getFirstName(),
            lastName: $user->getLastName(),
            pin: $user->getPin(),
            phoneNumber: $user->getPhoneNumber(),
            activated: true === $user->getActivated() ? 'Yes' : 'No'
        );
    }

    /**
     * @param User[] $users
     * @return UserViewModel[]
     */
    public static function usersToUserViewModels(array $users): array
    {
        return \array_map(
            fn (User $user) => self::userToUserViewModel($user),
            $users
        );
    }

    /**
     * @param array<string, mixed> $user
     */
    public static function userViewModelFromDbResult(array $user): UserViewModel
    {
        return new UserViewModel(
            id: $user['id'],
            email: $user['email'],
            firstName: $user['first_name'],
            lastName: $user['last_name'],
            pin: $user['pin'],
            phoneNumber: $user['phone_number'],
            activated: true === (bool) $user['activated'] ? 'Yes' : 'No',
        );
    }

    /**
     * @param array<int, array<string, mixed>> $users
     * @return UserViewModel[]
     */
    public static function userViewModelsFromDbResults(array $users): array
    {
        return \array_map(
            /** @var array<string, mixed> $user */
            fn(array $user) => self::userViewModelFromDbResult($user),
            $users
        );
    }
}
