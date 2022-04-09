<?php

namespace App\Repository;

use App\Entity\QuizResultCheer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizResultCheer|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizResultCheer|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizResultCheer[]    findAll()
 * @method QuizResultCheer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizResultCheerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResultCheer::class);
    }

    // /**
    //  * @return QuizResultCheer[] Returns an array of QuizResultCheer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuizResultCheer
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
