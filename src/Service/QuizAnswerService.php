<?php

namespace App\Service;

use App\Entity\QuizAnswer;
use App\Repository\QuizAnswerRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizAnswerService
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em, QuizAnswerRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }
    public function getAnswerByToken(string $token): ?QuizAnswer
    {
        return $this->repo->findOneBy([
            'answerToken' => $token
        ]);
    }
}
