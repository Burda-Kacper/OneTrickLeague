<?php

namespace App\ServiceQuiz;

use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use App\Repository\QuizUserAnsweredRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizUserAnsweredService
{
    /**
     * @var QuizUserAnsweredRepository $repo
     */
    private QuizUserAnsweredRepository $repo;

    /**
     * @param QuizUserAnsweredRepository $repo
     */
    public function __construct(QuizUserAnsweredRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Quiz $quiz
     *
     * @return QuizUserAnswered|null
     */
    public function getNewQuizUserAnswered(Quiz $quiz): ?QuizUserAnswered
    {
        return $this->repo->getNewQuizUserAnswered($quiz);
    }

    /**
     * @param int $quaId
     * @param Quiz $quiz
     *
     * @return QuizUserAnswered|null
     */
    public function getQuizUserAnsweredByIdQuiz(int $quaId, Quiz $quiz): ?QuizUserAnswered
    {
        return $this->repo->findOneBy([
            'id' => $quaId,
            'quiz' => $quiz
        ]);
    }

    /**
     * @param Quiz $quiz
     *
     * @return array
     */
    public function getQuizUserAnsweredByQuiz(Quiz $quiz): array
    {
        return $this->repo->findBy([
            'quiz' => $quiz
        ]);
    }
}
