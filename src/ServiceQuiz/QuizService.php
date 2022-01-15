<?php

namespace App\ServiceQuiz;

use App\DataStructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizAnswer;
use App\Entity\QuizQuestion;
use App\Entity\QuizSaved;
use App\Entity\QuizUserAnswered;
use App\Entity\User;
use App\Repository\QuizRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\QuizMessage;
use App\ServiceCommon\TokenService;

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
    private $quizResultCheerService;
    private $quizSavedService;

    public function __construct(
        EntityManagerInterface $em,
        QuizRepository $repo,
        QuizQuestionService $quizQuestionService,
        QuizUserAnsweredService $quizUserAnsweredService,
        QuizAnswerService $quizAnswerService,
        QuizResultCheerService $quizResultCheerService,
        QuizSavedService $quizSavedService
    ) {
        $this->repo = $repo;
        $this->em = $em;
        $this->quizQuestionService = $quizQuestionService;
        $this->quizUserAnsweredService = $quizUserAnsweredService;
        $this->quizAnswerService = $quizAnswerService;
        $this->quizResultCheerService = $quizResultCheerService;
        $this->quizSavedService = $quizSavedService;
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

    public function startQuiz(string $ip, ?User $user, ?string $quizSavedToken): MainResponse
    {
        $canStartQuiz = $this->limitQuizesForIp($ip);
        if (!$canStartQuiz) {
            return new MainResponse(false, QuizMessage::QUIZ_TOO_MANY_STARTED);
        }
        $quiz = new Quiz;
        $quizSaved = null;

        if ($quizSavedToken) {
            $quizSaved = $this->quizSavedService->getQuizSavedByToken($quizSavedToken);
            $questionsResponse = $this->quizSavedService->getQuestionsForQuizSavedToken($quizSavedToken);
            if (!$questionsResponse->getSuccess()) {
                return $questionsResponse;
            }
            $questions = $questionsResponse->getData();
        } else {
            $questions = $this->quizQuestionService->getQuestionsForQuiz();
        }


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

        $quizUserAnswers = $this->quizUserAnsweredService->getQuizUserAnsweredByQuiz($quiz);
        $quizSaved = $this->assignQuizSavedToQuiz($quiz, $quizSaved, $quizUserAnswers);
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
            return new MainResponse(false, QuizMessage::QUIZ_NOT_FOUND);
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
            return new MainResponse(false, QuizMessage::QUIZ_NOT_FOUND);
        }
        $quizTimeframe = (new DateTime('now'))->modify($this::QUIZ_TIMEOUT_FRAME);
        if ($quiz->getStarted() < $quizTimeframe) {
            $quiz->setIsValid(false);
            $this->em->persist($quiz);
            $this->em->flush();
            return new MainResponse(false, QuizMessage::QUIZ_TOOK_TOO_LONG);
        }
        $answer = null;
        if ($answerToken) {
            $answer = $this->quizAnswerService->getAnswerByToken($answerToken);
            if (!$answer) {
                return new MainResponse(false, QuizMessage::QUIZ_ANSWER_INVALID);
            }
        }
        $qua = $this->quizUserAnsweredService->getQuizUserAnsweredByIdQuiz($quaId, $quiz);
        if (!$qua) {
            return new MainResponse(false, QuizMessage::QUIZ_ANSWER_INVALID);
        }
        $isAnswerFromQuestion = true;
        if ($answer) {
            $isAnswerFromQuestion = $this->validateAnswerForQuestion($answer, $qua->getQuestion());
        }
        if (!$isAnswerFromQuestion) {
            return new MainResponse(false, QuizMessage::QUIZ_ANSWER_INVALID);
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

    public function getQuizResults(Quiz $quiz): array
    {
        $quas = $this->quizUserAnsweredService->getQuizUserAnsweredByQuiz($quiz);
        $result = [
            'maxScore' => count($quas),
            'score' => 0
        ];
        foreach ($quas as $qua) {
            $answer = $qua->getAnswer();
            if ($answer) {
                if ($answer->getIsCorrect()) {
                    $result['score']++;
                }
            }
        }
        $result['cheer'] = $this->quizResultCheerService->getQuizResultCheerByScore($result['score'])->getText();
        return $result;
    }

    public function assignQuizSavedToQuiz(Quiz $quiz, ?QuizSaved $quizSaved, array $quizUserAnswers): string
    {
        return $this->quizSavedService->assignQuizSavedToQuiz($quiz, $quizSaved, $quizUserAnswers);
    }

    public function getQuizSavedByToken(string $quizSavedToken): ?QuizSaved
    {
        return $this->quizSavedService->getQuizSavedByToken($quizSavedToken);
    }

    public function finishQuiz(Quiz $quiz): Quiz
    {
        $quiz->setIsFinished(true);
        $this->em->persist($quiz);
        $this->em->flush($quiz);
        return $quiz;
    }

    public function getUserQuizes(User $user, int $amount = 0): array
    {
        return $this->repo->findBy([
            'user' => $user,
            'isFinished' => 1,
            'isValid' => 1
        ], [
            'started' => "DESC"
        ], $amount);
    }

    public function addQuizQuestion(User $user, string $question, array $answers): MainResponse
    {
        $quizQuestion = $this->quizQuestionService->createQuizQuestion($user, $question);
        $isAddingAnswersSuccessful = $this->quizAnswerService->createQuizAnswers($quizQuestion, $answers);

        if ($isAddingAnswersSuccessful) {
            return new MainResponse(true, QuizMessage::QUIZ_NEW_QUESTION_SUCCESS);
        }

        return new MainResponse(false, QuizMessage::QUIZ_NEW_QUESTION_ERROR);
    }
}
