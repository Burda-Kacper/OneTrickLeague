<?php

namespace App\Controller;

use App\DataStructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use App\ServiceQuiz\QuizService;
use App\Error\QuizError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends AbstractController
{

    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function enter(): Response
    {
        return $this->render('quiz/enter.html.twig');
    }

    public function enterSaved(string $quizSavedToken): Response
    {
        $quizSaved = $this->quizService->getQuizSavedByToken($quizSavedToken);
        if ($quizSaved) {
            return $this->render('quiz/enter.html.twig', [
                'quizSaved' => $quizSaved
            ]);
        }
        return $this->render('quiz/enter.html.twig', [
            'quizSavedNotFound' => true
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $quizSavedToken = $request->get("quizSavedToken");
        $user = $this->getUser();
        $ip = $request->getClientIp();
        //ETODO: Handle start quiz errors
        $response = $this->quizService->startQuiz($ip, $user, $quizSavedToken);
        $token = $response->getData();
        return $this->getNewQuestion($token);
    }

    private function getNewQuestion(string $token): JsonResponse
    {
        $newQuestion = $this->quizService->getNewQuestion($token);
        if (!$newQuestion->getSuccess()) {
            return new JsonResponse($newQuestion->toJsonResponse());
        }
        $responseData = $newQuestion->getData();
        if ($responseData['text'] === "NEXT") {
            $response = $this->getNextQuestionResponse($responseData['qua'], $token);
            return new JsonResponse($response->toJsonResponse());
        }
        if ($responseData['text']  === "DONE") {
            $response = $this->getFinishQuizResponse($responseData['quiz'], $token);
            return new JsonResponse($response->toJsonResponse());
        }
        $response = new MainResponse(false, QuizError::QUIZ_UNKNOWN_ERROR);
        return new JsonResponse($response->toJsonResponse());
    }

    public function answer(Request $request): JsonResponse
    {
        $quaId = intval($request->get("quaId"));
        $token = $request->get("token");
        $answerToken = $request->get("answerToken");
        $quaSaveStatus = $this->quizService->setQuizUserAnswer($token, $quaId, $answerToken);
        if (!$quaSaveStatus->getSuccess()) {
            return new JsonResponse($quaSaveStatus->toJsonResponse());
        }
        return $this->getNewQuestion($token);
    }

    private function getNextQuestionResponse(QuizUserAnswered $qua, string $token): MainResponse
    {
        return new MainResponse(true, $this->renderView('quiz/_question.html.twig', [
            'qua' => $qua,
            'token' => $token
        ]));
    }

    private function getFinishQuizResponse(Quiz $quiz, string $token): MainResponse
    {
        $quiz = $this->quizService->finishQuiz($quiz);
        $quizResults = $this->quizService->getQuizResults($quiz);
        $quizSavedToken = $this->quizService->createQuizSaved($quiz);
        return new MainResponse(true, $this->renderView('quiz/_finish.html.twig', [
            'token' => $token,
            'quizResults' => $quizResults,
            'quizSavedToken' => $quizSavedToken
        ]));
    }
}
