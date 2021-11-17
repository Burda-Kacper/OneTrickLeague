<?php

namespace App\Controller;

use App\DataSctructure\MainResponse;
use App\Service\QuizService;
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

    public function start(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $ip = $request->getClientIp();
        $response = $this->quizService->startQuiz($ip, $user);
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
            $response = new MainResponse(true, $this->renderView('quiz/_question.html.twig', [
                'qua' => $responseData['qua'],
                'token' => $token
            ]));
            return new JsonResponse($response->toJsonResponse());
        }
        if ($responseData['text']  === "DONE") {
            $response = new MainResponse(true, $this->renderView('quiz/_finish.html.twig', [
                'token' => $token
            ]));
            return new JsonResponse($response->toJsonResponse());
        }
        $response = new MainResponse(false, "Wystąpił nieokreślony błąd z quizem.");
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
}
