<?php

namespace App\Repository;

use App\Entity\QuizQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizQuestion[]    findAll()
 * @method QuizQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizQuestionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizQuestion::class);
    }

    /**
     * @return int
     */
    public function getLastQuestionId(): int
    {
        $qb = $this->createQueryBuilder('question')
            ->select("question.id")
            ->orderBy("question.id", "DESC")
            ->setMaxResults(1);
        return $qb->getQuery()->execute()[0]['id'];
    }

    /**
     * @param array $includedIds
     * @param array $excludedIds
     *
     * @return array
     */
    public function getQuestionsForQuiz(array $includedIds, array $excludedIds): array
    {
        $qb = $this->createQueryBuilder('question')
            ->select("question")
            ->andWhere('question.id IN(:includedIds)')
            ->andWhere('question.id NOT IN(:excludedIds)')
            ->andWhere("question.isActive = 1")
            ->setParameter("includedIds", array_values($includedIds))
            ->setParameter("excludedIds", array_values($excludedIds));
        return $qb->getQuery()->execute();
    }

    // /**
    //  * @return QuizQuestion[] Returns an array of QuizQuestion objects
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
    public function findOneBySomeField($value): ?QuizQuestion
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
