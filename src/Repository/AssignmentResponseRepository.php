<?php

namespace App\Repository;

use App\Entity\AssignmentResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssignmentResponse>
 *
 * @method AssignmentResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssignmentResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssignmentResponse[]    findAll()
 * @method AssignmentResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignmentResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssignmentResponse::class);
    }

    public function add(AssignmentResponse $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AssignmentResponse $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function updateAssignmentResponsePath(int $assignmentResponseId, string $path): int
    {
        return $this->_em->createQueryBuilder()
            ->update('App:AssignmentResponse', 'a')
            ->set('a.filePath', ':path')
            ->where('a.id = :id')
            ->setParameter('path', $path)
            ->setParameter('id', $assignmentResponseId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return AssignmentResponse[]
     */
    public function findByAssignmentId(int $assignmentId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.assignment = :id')
            ->setParameter('id', $assignmentId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return AssignmentResponse[] Returns an array of AssignmentResponse objects
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

//    public function findOneBySomeField($value): ?AssignmentResponse
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
