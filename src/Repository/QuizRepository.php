<?php

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function getQuizesCountByIpAndDatetime(string $ip, DateTime $datetime): int
    {
        $qb = $this->createQueryBuilder('quiz')
            ->select("count(quiz.id) AS count")
            ->andWhere('quiz.ip = :ip')
            ->andWhere('quiz.started > :datetime')
            ->setParameter("ip", $ip)
            ->setParameter("datetime", $datetime);
        return $qb->getQuery()->execute()[0]['count'];
    }

    public function getQuizCacheInfoForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select("q")
            ->andWhere('q.isFinished = 1')
            ->andWhere('q.isValid = 1')
            ->andWhere('q.user = :user')
            ->setParameter('user', $user);
        return $qb->getQuery()->execute();
    }

    // /**
    //  * @return Quiz[] Returns an array of Quiz objects
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
    public function findOneBySomeField($value): ?Quiz
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
