<?php

namespace App\Controller;

use App\DataSctructure\MainResponse;
use App\Service\QuizService;
use App\Service\TokenService;
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
        return new JsonResponse($response->toJsonResponse());
    }
}
