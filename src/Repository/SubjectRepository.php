<?php

namespace App\Repository;

use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Subject $entity, bool $flush = true): void
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
    public function update(Subject $entity, bool $flush = true): void
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
    public function remove(Subject $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Subject[]
     */
    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.teacher = :teacherId')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Subject[]
     */
    public function findByStudyGroup(int $studyGroupId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.studyGroup = :studyGroupId')
            ->setParameter('studyGroupId', $studyGroupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Subject[]
     */
    public function findBySchoolId(int $schoolId): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin(
                's.studyGroup', 'sg',
                Join::WITH,
                's.studyGroup = sg.id'
            )
            ->andWhere('sg.school = :schoolId')
            ->orderBy('sg.number', 'ASC')
            ->addOrderBy('sg.name', 'ASC')
            ->setParameter('schoolId', $schoolId)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Subject[] Returns an array of Subject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subject
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
