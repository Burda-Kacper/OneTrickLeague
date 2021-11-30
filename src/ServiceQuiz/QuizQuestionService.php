<?php

namespace App\ServiceQuiz;

use App\Repository\QuizQuestionRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizQuestionService
{
    const QUESTIONS_IN_QUIZ = 3;
    const QUESTIONS_IDS_BATCH = 10;

    private $em;
    private $repo;

    public function __construct(EntityManagerInterface $em, QuizQuestionRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function getQuestionsForQuiz(): array
    {
        $lastQuestionId = $this->repo->getLastQuestionId();
        $excludedIds = [0];
        $questions = [];
        while (count($questions) < $this::QUESTIONS_IN_QUIZ) {
            $questionIds = $this->generateQuestionIds($lastQuestionId);
            $questions = array_merge($questions, $this->repo->getQuestionsForQuiz($questionIds, $excludedIds));
            $excludedIds = array_merge($excludedIds, $questionIds);
        }
        return $this->reduceQuestionsAmount($questions);
    }

    private function generateQuestionIds(int $lastQuestionId): array
    {
        $questionIds = [];
        for ($count = 0; $count < $this::QUESTIONS_IDS_BATCH; $count++) {
            $questionIds[] = rand(1, $lastQuestionId);
        }
        return $questionIds;
    }

    private function reduceQuestionsAmount(array $questions): array
    {
        if (count($questions) > $this::QUESTIONS_IN_QUIZ) {
            return array_slice($questions, 0, $this::QUESTIONS_IN_QUIZ);
        }
        return $questions;
    }
}
