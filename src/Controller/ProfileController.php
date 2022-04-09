<?php

namespace App\Controller;

use App\Message\ProfileMessage;
use App\ServiceProfile\ProfileService;
use App\ServiceQuiz\QuizService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    /**
     * @var ProfileService $profileService
     */
    private ProfileService $profileService;

    /**
     * @var QuizService $quizService
     */
    private QuizService $quizService;

    /**
     * @param ProfileService $profileService
     * @param QuizService $quizService
     */
    public function __construct(ProfileService $profileService, QuizService $quizService)
    {
        $this->profileService = $profileService;
        $this->quizService = $quizService;
    }

    /**
     * @return Response
     */
    public function profile(): Response
    {
        $user = $this->getUser();

        if (!$this->profileService->getUserQuizCache($user)) {
            $this->profileService->refreshResultCache($user);
        }

        return $this->render('profile/profile.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function section(Request $request): JsonResponse
    {
        $section = $request->get('section');

        switch ($section) {
            case 'quiz':
                return $this->sectionQuiz();
            case 'profile':
                return $this->sectionProfile();
        }

        return new JsonResponse([
            'success' => false,
            'data' => ProfileMessage::PROFILE_WRONG_SECTION
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function sectionProfile(): JsonResponse
    {
        $user = $this->getUser();
        $availablePictures = $user->getAvailablePictures();

        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/profile/_profile.html.twig', [
                'availablePictures' => $availablePictures
            ])
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function sectionQuiz(): JsonResponse
    {
        $user = $this->getUser();
        $userQuizCache = $this->profileService->getUserQuizCache($user);
        $userLastQuizes = $this->quizService->getUserQuizes($user, 5);

        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/quiz/_quiz.html.twig', [
                'userQuizCache' => $userQuizCache,
                'userLastQuizes' => $userLastQuizes
            ])
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function picture(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $pictureId = $request->get("pictureId");
        $response = $this->profileService->setProfilePicture($user, intval($pictureId));

        return new JsonResponse($response->toJsonResponse());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function password(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $passwords = $request->get("passwords");
        $response = $this->profileService->changePassword($user, $passwords);

        return new JsonResponse($response->toJsonResponse());
    }
}
