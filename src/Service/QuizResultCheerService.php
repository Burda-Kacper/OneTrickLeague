<?php

namespace App\Service;

use App\Entity\QuizResultCheer;
use App\Repository\QuizResultCheerRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizResultCheerService
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em, QuizResultCheerRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }
    public function getQuizResultCheerByScore(int $score): ?QuizResultCheer
    {
        $cheers = $this->repo->findBy([
            'score' => $score
        ]);
        return $cheers[array_rand($cheers)];
    }
}
