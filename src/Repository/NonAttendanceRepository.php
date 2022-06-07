<?php

namespace App\Repository;

use App\Entity\NonAttendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NonAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method NonAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method NonAttendance[]    findAll()
 * @method NonAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NonAttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NonAttendance::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(NonAttendance $entity, bool $flush = true): void
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
    public function remove(NonAttendance $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return NonAttendance[]
     */
    public function findByUserIdAndSubjectId(int $userId, int $subjectId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.student = :userId')
            ->andWhere('n.subject = :subjectId')
            ->setParameter('userId', $userId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return NonAttendance[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.student = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return NonAttendance[]
     */
    public function findBySubjectId(int $subjectId): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.subject = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    public function updateMotivated(int $id, bool $isMotivated): int
    {
        return $this->_em->createQueryBuilder()
            ->update('App:NonAttendance', 'n')
            ->set('n.motivated', ':isMotivated')
            ->where('n.id = :id')
            ->setParameter('id', $id)
            ->setParameter('isMotivated', $isMotivated)
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return NonAttendance[] Returns an array of NonAttendance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NonAttendance
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
