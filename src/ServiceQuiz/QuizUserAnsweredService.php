<?php

namespace App\ServiceQuiz;

use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use App\Repository\QuizUserAnsweredRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizUserAnsweredService
{
    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em, QuizUserAnsweredRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }
    public function getNewQuizUserAnswered(Quiz $quiz): ?QuizUserAnswered
    {
        return $this->repo->getNewQuizUserAnswered($quiz);
    }
    public function getQuizUserAnsweredByIdQuiz(int $quaId, Quiz $quiz)
    {
        return $this->repo->findOneBy([
            'id' => $quaId,
            'quiz' => $quiz
        ]);
    }
    public function getQuizUserAnsweredByQuiz(Quiz $quiz): array
    {
        return $this->repo->findBy([
            'quiz' => $quiz
        ]);
    }
}
