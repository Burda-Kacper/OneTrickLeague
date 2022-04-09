<?php

namespace App\ServiceQuiz;

use App\Entity\QuizResultCheer;
use App\Repository\QuizResultCheerRepository;

class QuizResultCheerService
{
    /**
     * @var QuizResultCheerRepository
     */
    private QuizResultCheerRepository $repo;

    /**
     * @param QuizResultCheerRepository $repo
     */
    public function __construct(QuizResultCheerRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param int $score
     *
     * @return QuizResultCheer|null
     */
    public function getQuizResultCheerByScore(int $score): ?QuizResultCheer
    {
        $cheers = $this->repo->findBy([
            'score' => $score
        ]);

        return $cheers[array_rand($cheers)];
    }
}
