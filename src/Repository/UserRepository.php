<?php

namespace App\Repository;

use App\Dto\ViewModel\UserViewModel;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private DenormalizerInterface $normalizer
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function update(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findBySchoolAndRole(int $schoolId, string $role): array
    {
        return $this->_em
            ->getConnection()
            ->executeQuery(/** @lang SQL */'
                SELECT * FROM "user" u
                WHERE u.school_id = :schoolId
                    AND u.roles::jsonb ?? :role 
                ORDER BY u.first_name ASC' ,
                [
                    'schoolId' => $schoolId,
                    'role' => $role
                ]
            )->fetchAllAssociative();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByStudyGroupAndRole(int $studyGroupId, string $role): array
    {
        return $this->_em
            ->getConnection()
            ->executeQuery(/** @lang SQL */'
                SELECT * FROM "user" u
                WHERE u.study_group_id = :studyGroupId
                    AND u.roles::jsonb ?? ' . ":role",
                [
                    'studyGroupId' => $studyGroupId,
                    'role' => $role
                ]
            )->fetchAllAssociative();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findNonClassMasterTeachersBySchoolId(int $schoolId): array
    {
        return $this->_em
            ->getConnection()
            ->executeQuery(/** @lang SQL */'
                SELECT u.* FROM "user" u
                WHERE u.school_id = :schoolId
                    AND u.roles::jsonb ??' . "'ROLE_TEACHER'" .
                'AND u.id NOT IN (
                    SELECT class_master_id from "study_group" sg)',
                [
                    'schoolId' => $schoolId,
                ]
            )->fetchAllAssociative();
    }

    public function findByActivationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.activationToken = :token')
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    public function findStudentsByStudyGroupId(int $studyGroupId): array
//    {
//        return $this->_em
//            ->getConnection()
//            ->executeQuery(/** @lang SQL */ '
//                SELECT * FROM "user" u
//                WHERE u.s
//            ')
//    }



    // /**
    //  * @return UserDto[] Returns an array of UserDto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserDto
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
