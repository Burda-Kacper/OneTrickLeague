<?php

namespace App\Controller;

use App\DataStructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizUserAnswered;
use App\ServiceQuiz\QuizService;
use App\Message\QuizMessage;
use App\ServiceProfile\ProfileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends AbstractController
{

    /**
     * @var QuizService $quizService
     */
    private QuizService $quizService;

    /**
     * @var ProfileService
     */
    private ProfileService $profileService;

    /**
     * @param QuizService $quizService
     * @param ProfileService $profileService
     */
    public function __construct(QuizService $quizService, ProfileService $profileService)
    {
        $this->quizService = $quizService;
        $this->profileService = $profileService;
    }

    /**
     * @return Response
     */
    public function enter(): Response
    {
        return $this->render('quiz/enter.html.twig');
    }

    /**
     * @param string $quizSavedToken
     *
     * @return Response
     */
    public function enterSaved(string $quizSavedToken): Response
    {
        $quizSaved = $this->quizService->getQuizSavedByToken($quizSavedToken);

        if ($quizSaved) {
            return $this->render('quiz/enter.html.twig', [
                'quizSaved' => $quizSaved
            ]);
        }

        return $this->render('quiz/enter.html.twig', [
            'quizSavedNotFound' => true,
            'quizSavedNotFoundMessage' => QuizMessage::QUIZ_SAVED_NOT_FOUND
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function start(Request $request): JsonResponse
    {
        $quizSavedToken = $request->get("quizSavedToken");
        $user = $this->getUser();
        $ip = $request->getClientIp();
        $response = $this->quizService->startQuiz($ip, $user, $quizSavedToken);

        if (!$response->getSuccess()) {
            return new JsonResponse($response->toJsonResponse());
        }

        $token = $response->getData();

        return $this->getNewQuestion($token);
    }

    /**
     * @param string $token
     *
     * @return JsonResponse
     */
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

        if ($responseData['text'] === "DONE") {
            $response = $this->getFinishQuizResponse($responseData['quiz'], $token);

            return new JsonResponse($response->toJsonResponse());
        }

        $response = new MainResponse(false, QuizMessage::QUIZ_UNKNOWN_ERROR);

        return new JsonResponse($response->toJsonResponse());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
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

    /**
     * @param QuizUserAnswered $qua
     * @param string $token
     *
     * @return MainResponse
     */
    private function getNextQuestionResponse(QuizUserAnswered $qua, string $token): MainResponse
    {
        $answers = $qua->getQuestion()->getAnswers()->toArray();
        shuffle($answers);

        return new MainResponse(true, $this->renderView('quiz/_question.html.twig', [
            'qua' => $qua,
            'token' => $token,
            'answers' => $answers
        ]));
    }

    /**
     * @param Quiz $quiz
     * @param string $token
     *
     * @return MainResponse
     */
    private function getFinishQuizResponse(Quiz $quiz, string $token): MainResponse
    {
        $user = $this->getUser();
        $quiz = $this->quizService->finishQuiz($quiz);
        $quizResults = $this->quizService->getQuizResults($quiz);
        $quizSavedToken = $quiz->getQuizSaved()->getToken();
        $this->profileService->refreshResultCache($user);

        return new MainResponse(true, $this->renderView('quiz/_finish.html.twig', [
            'token' => $token,
            'quizResults' => $quizResults,
            'quizSavedToken' => $quizSavedToken
        ]));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addQuizQuestion(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $question = $request->get("question");
        $answers = $request->get("answers");
        $response = $this->quizService->addQuizQuestion($user, $question, $answers);

        return new JsonResponse($response->toJsonResponse());
    }
}
