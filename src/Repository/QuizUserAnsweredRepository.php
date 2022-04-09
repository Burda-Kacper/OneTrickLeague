<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizUserAnswered|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizUserAnswered|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizUserAnswered[]    findAll()
 * @method QuizUserAnswered[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizUserAnsweredRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizUserAnswered::class);
    }

    /**
     * @param Quiz $quiz
     *
     * @return QuizUserAnswered|null
     */
    public function getNewQuizUserAnswered(Quiz $quiz): ?QuizUserAnswered
    {
        $qb = $this->createQueryBuilder('qua')
            ->select("qua")
            ->andWhere('qua.quiz = :quiz')
            ->andWhere("qua.answer IS NULL")
            ->andWhere("qua.active = 1")
            ->setMaxResults(1)
            ->setParameter("quiz", $quiz);
        $result = $qb->getQuery()->execute();

        if ($result) {
            return $result[0];
        }

        return null;
    }

    // /**
    //  * @return QuizUserAnswered[] Returns an array of QuizUserAnswered objects
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
    public function findOneBySomeField($value): ?QuizUserAnswered
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
