<?php

namespace App\Service;

use App\DataSctructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use App\Entity\User;
use App\Repository\QuizRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class QuizService
{
    //ETODO: Move ALL Service configs to backend of some sort
    const QUIZ_LIMIT_TIME = "-1 day";
    const QUIZ_LIMIT_AMOUNT = 100;

    private $em;
    private $repo;
    private $quizQuestionService;

    public function __construct(EntityManagerInterface $em, QuizRepository $repo, QuizQuestionService $quizQuestionService)
    {
        $this->repo = $repo;
        $this->em = $em;
        $this->quizQuestionService = $quizQuestionService;
    }

    public function generateToken(): string
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

    public function startQuiz(string $ip, ?User $user): MainResponse
    {
        $canStartQuiz = $this->limitQuizesForIp($ip);
        if (!$canStartQuiz) {
            return new MainResponse(false, "Zaczynasz zbyt dużo quizów! Odczekaj proszę trochę przed kolejnym.");
        }
        $quiz = new Quiz;

        $questions = $this->quizQuestionService->getQuestionsForQuiz();
        foreach ($questions as $question) {
            $questionAnswered = new QuizUserAnswered;
            $questionAnswered->setQuiz($quiz);
            $questionAnswered->setQuestion($question);
            $this->em->persist($questionAnswered);
        }

        $token = $this->generateToken();
        $quiz->setToken($token);
        $quiz->setUser($user);
        $quiz->setIp($ip);
        $this->em->persist($quiz);
        $this->em->flush();
        return new MainResponse(true);
    }

    public function limitQuizesForIp(string $ip): bool
    {
        $datetime = new DateTime('now');
        $datetime->modify($this::QUIZ_LIMIT_TIME);
        $quizesAmount = $this->repo->getQuizesCountByIpAndDatetime($ip, $datetime);
        if ($quizesAmount >= $this::QUIZ_LIMIT_AMOUNT) {
            return false;
        }
        return true;
    }
}
