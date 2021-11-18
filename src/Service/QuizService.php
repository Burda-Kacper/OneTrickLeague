<?php

namespace App\Service;

use App\DataSctructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizAnswer;
use App\Entity\QuizQuestion;
use App\Entity\QuizUserAnswered;
use App\Entity\User;
use App\Repository\QuizRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use QuizError;

class QuizService
{
    //ETODO: Move ALL Service configs to backend of some sort
    const QUIZ_LIMIT_TIME = "-1 day";
    const QUIZ_LIMIT_AMOUNT = 100;
    const QUIZ_TIMEOUT_FRAME = "-4 minutes";

    private $em;
    private $repo;
    private $quizQuestionService;
    private $quizUserAnsweredService;
    private $quizAnswerService;

    public function __construct(
        EntityManagerInterface $em,
        QuizRepository $repo,
        QuizQuestionService $quizQuestionService,
        QuizUserAnsweredService $quizUserAnsweredService,
        QuizAnswerService $quizAnswerService
    ) {
        $this->repo = $repo;
        $this->em = $em;
        $this->quizQuestionService = $quizQuestionService;
        $this->quizUserAnsweredService = $quizUserAnsweredService;
        $this->quizAnswerService = $quizAnswerService;
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
            return new MainResponse(false, QuizError::QUIZ_TOO_MANY_STARTED);
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
        return new MainResponse(true, $token);
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

    public function getNewQuestion(string $token): MainResponse
    {
        $quiz = $this->getQuizByToken($token);
        if (!$quiz) {
            return new MainResponse(false, QuizError::QUIZ_NOT_FOUND);
        }
        $quizUserAnswered = $this->quizUserAnsweredService->getNewQuizUserAnswered($quiz);
        if ($quizUserAnswered) {
            return new MainResponse(true, [
                'text' => "NEXT",
                'qua' => $quizUserAnswered
            ]);
        }
        return new MainResponse(true, [
            'text' => "DONE",
            'quiz' => $quiz
        ]);
    }

    public function setQuizUserAnswer(string $quizToken, int $quaId, string $answerToken): MainResponse
    {
        $quiz = $this->getQuizByToken($quizToken);
        if (!$quiz) {
            return new MainResponse(false, QuizError::QUIZ_NOT_FOUND);
        }
        $quizTimeframe = (new DateTime('now'))->modify($this::QUIZ_TIMEOUT_FRAME);
        if ($quiz->getStarted() < $quizTimeframe) {
            $quiz->setIsValid(false);
            $this->em->persist($quiz);
            $this->em->flush();
            return new MainResponse(false, QuizError::QUIZ_TOOK_TOO_LONG);
        }
        $answer = null;
        if ($answerToken) {
            $answer = $this->quizAnswerService->getAnswerByToken($answerToken);
            if (!$answer) {
                return new MainResponse(false, QuizError::QUIZ_ANSWER_INVALID);
            }
        }
        $qua = $this->quizUserAnsweredService->getQuizUserAnsweredByIdQuiz($quaId, $quiz);
        if (!$qua) {
            return new MainResponse(false, QuizError::QUIZ_ANSWER_INVALID);
        }
        $isAnswerFromQuestion = true;
        if ($answer) {
            $isAnswerFromQuestion = $this->validateAnswerForQuestion($answer, $qua->getQuestion());
        }
        if (!$isAnswerFromQuestion) {
            return new MainResponse(false, QuizError::QUIZ_ANSWER_INVALID);
        }

        $qua->setActive(false);
        if (!$qua->getAnswer()) {
            $qua->setAnswer($answer);
            $this->em->persist($qua);
            $this->em->flush();
        }
        return new MainResponse(true);
    }

    private function validateAnswerForQuestion(QuizAnswer $answer, QuizQuestion $question)
    {
        return $answer->getQuestion() === $question;
    }
}
