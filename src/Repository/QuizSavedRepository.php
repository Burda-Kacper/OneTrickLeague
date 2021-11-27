<?php

namespace App\Repository;

use App\Entity\QuizSaved;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizSaved|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizSaved|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizSaved[]    findAll()
 * @method QuizSaved[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizSavedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizSaved::class);
    }

    // /**
    //  * @return QuizSaved[] Returns an array of QuizSaved objects
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
    public function findOneBySomeField($value): ?QuizSaved
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
