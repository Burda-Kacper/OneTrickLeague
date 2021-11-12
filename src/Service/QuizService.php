<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizService
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em, QuizRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function generateToken()
    {
        $token = TokenService::generateToken();
        $takenToken = $this->getQuizByToken($token);
        if ($takenToken) {
            return $this->generateToken();
        }
        return $token;
    }

    public function getQuizByToken(string $token): ?Quiz
    {
        return $this->repo->findOneBy([
            'token' => $token
        ]);
    }

    public function startQuiz(string $ip, ?User $user)
    {
        $quiz = new Quiz;
        $token = $this->generateToken();
        $quiz->setToken($token);
        $quiz->setUser($user);
        $quiz->setIp($ip);
        $this->em->persist($quiz);
        $this->em->flush();
        return $token;
    }
}
