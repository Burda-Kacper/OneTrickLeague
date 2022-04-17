<?php

namespace App\Controller;

use App\Message\ProfileMessage;
use App\Message\QuizMessage;
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

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'isLoggedUser' => true,
            'isPublic' => true
        ]);
    }

    /**
     * @param string $userUrl
     *
     * @return Response
     */
    public function profileDetails(string $userUrl): Response
    {
        $user = $this->profileService->getUserByUrl($userUrl);

        if (!$user) {
            return new Response(null, 404);
        }

        if (!$this->profileService->getUserQuizCache($user)) {
            $this->profileService->refreshResultCache($user);
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'isLoggedUser' => false,
            'isPublic' => $user->getIsPublic() || ($this->getUser() && $this->getUser()->getId() === $user->getId())
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function section(Request $request): JsonResponse
    {
        $section = $request->get('section');
        $userUrl = $request->get("userUrl");

        switch ($section) {
            case 'quiz':
                return $this->sectionQuiz($userUrl);
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
     * @param string $userUrl
     *
     * @return JsonResponse
     */
    public function sectionQuiz(string $userUrl): JsonResponse
    {
        $user = $this->profileService->getUserByUrl($userUrl);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => ProfileMessage::PROFILE_USER_NOT_FOUND
            ]);
        }

        $isLoggedUser = $this->getUser() && ($this->getUser()->getId() === $user->getId());

        if (!$isLoggedUser && !$user->getIsPublic()) {
            return new JsonResponse([
                'success' => false,
                'data' => ProfileMessage::PROFILE_USER_NOT_PUBLIC
            ]);
        }

        $userQuizCache = $this->profileService->getUserQuizCache($user);
        $userLastQuizes = $this->quizService->getUserQuizes($user, 5);

        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/quiz/_quiz.html.twig', [
                'userQuizCache' => $userQuizCache,
                'userLastQuizes' => $userLastQuizes,
                'isLoggedUser' => $isLoggedUser
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function quizDetails(Request $request): JsonResponse
    {
        $quizToken = $request->get("quizToken");
        $quiz = $this->quizService->getQuizByToken($quizToken);

        if (!$quiz) {
            return new JsonResponse([
                'success' => false,
                'data' => QuizMessage::QUIZ_NOT_FOUND
            ]);
        }

        $isLoggedUser = $this->getUser() && ($this->getUser()->getId() === $quiz->getUser()->getId());

        if (!$isLoggedUser && !$quiz->getUser()->getIsPublic()) {
            return new JsonResponse([
                'success' => false,
                'data' => ProfileMessage::PROFILE_USER_NOT_PUBLIC
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/quiz/_quizDetails.html.twig', [
                'quiz' => $quiz,
                'isLoggedUser' => $isLoggedUser
            ])
        ]);
    }
}
