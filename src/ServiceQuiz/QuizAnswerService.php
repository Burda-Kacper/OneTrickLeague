<?php

namespace App\ServiceQuiz;

use App\Entity\QuizAnswer;
use App\Entity\QuizQuestion;
use App\Repository\QuizAnswerRepository;
use App\ServiceCommon\TokenService;
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

    private function generateQuizAnswerToken(): string
    {
        $token = TokenService::generateToken();
        $takenToken = $this->getAnswerByToken($token);
        if ($takenToken) {
            return $this->generateQuizAnswerToken();
        }
        return $token;
    }

    public function createQuizAnswers(QuizQuestion $quizQuestion, array $answers): bool
    {
        $allowedAnswers = ['correct', "wrong1", "wrong2", "wrong3"];
        $success = true;

        foreach ($allowedAnswers as $answerIndex) {
            if (!isset($answers[$answerIndex])) {
                $success = false;
                break;
            }

            $quizAnswer = new QuizAnswer;
            $quizAnswer->setQuestion($quizQuestion);
            $quizAnswer->setAnswer($answers[$answerIndex]);
            $quizAnswer->setAnswerToken($this->generateQuizAnswerToken());

            if ($answerIndex === 'correct') {
                $quizAnswer->setIsCorrect(true);
            }

            $this->em->persist($quizAnswer);
        }

        $this->em->flush();
        return $success;
    }
}
