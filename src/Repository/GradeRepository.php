<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Grade $entity, bool $flush = true): void
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
    public function remove(Grade $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Grade[]
     */
    public function findByUserIdAndSubjectId(int $userId, int $subjectId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.student = :userId')
            ->andWhere('g.subject = :subjectId')
            ->setParameter('userId', $userId)
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Grade[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.student = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Grade[]
     */
    public function findBySubjectId(int $subjectId): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.subject = :subjectId')
            ->setParameter('subjectId', $subjectId)
            ->getQuery()
            ->getResult();
    }

    public function updateGradeMark(int $id, int $mark): int
    {
        return $this->_em->createQueryBuilder()
            ->update('App:Grade', 'g')
            ->set('g.grade', ':grade')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->setParameter('grade', $mark)
            ->getQuery()
            ->execute();
    }

    // /**
    //  * @return Grade[] Returns an array of Grade objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grade
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
