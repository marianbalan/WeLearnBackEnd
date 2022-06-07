<?php

namespace App\Repository;

use App\Entity\Assignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 *
 * @method Assignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assignment[]    findAll()
 * @method Assignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function add(Assignment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(Assignment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Assignment $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeById(int $id): int
    {
        return $this->_em->createQueryBuilder()
            ->delete('App:Assignment', 'a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Assignment[]
     */
    public function findBySubject(int $subjectId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.subject = :subject')
            ->setParameter('subject', $subjectId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Assignment[]
     */
    public function findByTeacher(int $teacherId): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin(
                'a.subject', 's',
                Join::WITH,
                'a.subject = s.id'
            )
            ->innerJoin(
                's.studyGroup', 'sg',
                Join::WITH,
                's.studyGroup = sg.id'
            )
            ->where('s.teacher = :teacherId')
            ->orderBy('a.dueTo', 'ASC')
            ->setParameter('teacherId', $teacherId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Assignment[]
     */
    public function findByStudyGroup(int $studyGroupId): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin(
                'a.subject', 's',
                Join::WITH,
                'a.subject = s.id'
            )
            ->where('s.studyGroup = :studyGroupId')
            ->orderBy('a.dueTo', 'ASC')
            ->setParameter('studyGroupId', $studyGroupId)
            ->getQuery()
            ->getResult();
    }

    public function updateAssignmentPath(int $assignmentId, string $path): int
    {
        return $this->_em->createQueryBuilder()
            ->update('App:Assignment', 'a')
            ->set('a.requirementFilePath', ':path')
            ->where('a.id = :id')
            ->setParameter('path', $path)
            ->setParameter('id', $assignmentId)
            ->getQuery()
            ->execute();
    }

    public function updateAssignmentClosed(int $assignmentId, bool $closed): int
    {
        return $this->_em->createQueryBuilder()
            ->update('App:Assignment', 'a')
            ->set('a.closed', ':closed')
            ->where('a.id = :id')
            ->setParameter('closed', $closed)
            ->setParameter('id', $assignmentId)
            ->getQuery()
            ->execute();
    }

//    /**
//     * @return AssignmentService[] Returns an array of AssignmentService objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AssignmentService
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
