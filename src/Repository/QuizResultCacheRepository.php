<?php

namespace App\Repository;

use App\Entity\QuizResultCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizResultCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizResultCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizResultCache[]    findAll()
 * @method QuizResultCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizResultCacheRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResultCache::class);
    }

    // /**
    //  * @return QuizResultCache[] Returns an array of QuizResultCache objects
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
    public function findOneBySomeField($value): ?QuizResultCache
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
