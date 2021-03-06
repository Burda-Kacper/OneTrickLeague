<?php

namespace App\ServiceQuiz;

use App\Entity\QuizQuestion;
use App\Entity\User;
use App\Repository\QuizQuestionRepository;
use Doctrine\ORM\EntityManagerInterface;

class QuizQuestionService
{
    // Questions in quiz is NOT to be changed. Ever.
    const QUESTIONS_IN_QUIZ = 10;
    const QUESTIONS_IDS_BATCH = 20;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var QuizQuestionRepository $repo
     */
    private QuizQuestionRepository $repo;

    /**
     * @param EntityManagerInterface $em
     * @param QuizQuestionRepository $repo
     */
    public function __construct(EntityManagerInterface $em, QuizQuestionRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    /**
     * @return array
     */
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

    /**
     * @param int $lastQuestionId
     *
     * @return array
     */
    private function generateQuestionIds(int $lastQuestionId): array
    {
        $questionIds = [];

        for ($count = 0; $count < $this::QUESTIONS_IDS_BATCH; $count++) {
            $questionIds[] = rand(1, $lastQuestionId);
        }

        return $questionIds;
    }

    /**
     * @param array $questions
     *
     * @return array
     */
    private function reduceQuestionsAmount(array $questions): array
    {
        if (count($questions) > $this::QUESTIONS_IN_QUIZ) {
            return array_slice($questions, 0, $this::QUESTIONS_IN_QUIZ);
        }

        return $questions;
    }

    /**
     * @param User $user
     * @param string $question
     *
     * @return QuizQuestion
     */
    public function createQuizQuestion(User $user, string $question): QuizQuestion
    {
        $quizQuestion = new QuizQuestion;
        $quizQuestion->setOwner($user);
        $quizQuestion->setQuestion($question);

        $this->em->persist($quizQuestion);
        $this->em->flush();

        return $quizQuestion;
    }
}
