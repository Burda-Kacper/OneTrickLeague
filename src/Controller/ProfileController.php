<?php

namespace App\Controller;

use App\Error\ProfileError;
use App\ServiceProfile\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{

    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig');
    }

    public function section(Request $request): JsonResponse
    {
        //ETODO: Fetch real data :) 
        $section = $request->get('section');
        switch ($section) {
            case 'quiz':
                return $this->sectionQuiz();
            case 'profile':
                return $this->sectionProfile();
        }
        return new JsonResponse([
            'success' => false,
            'data' => ProfileError::PROFILE_WRONG_SECTION
        ]);
    }

    public function sectionProfile(): JsonResponse
    {
        $user = $this->getUser();
        $availablePictures = $user->getAvailablePictures();
        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/_profile.html.twig', [
                'availablePictures' => $availablePictures
            ])
        ]);
    }

    public function sectionQuiz(): JsonResponse
    {
        $user = $this->getUser();
        $userQuizCache = $this->profileService->getUserQuizCache($user);
        return new JsonResponse([
            'success' => true,
            'data' => $this->renderView('profile/section/_quiz.html.twig', [
                'userQuizCache' => $userQuizCache
            ])
        ]);
    }

    public function picture(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $pictureId = $request->get("pictureId");
        $response = $this->profileService->setProfilePicture($user, intval($pictureId));
        return new JsonResponse($response->toJsonResponse());
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->profileService->refreshResultCache($user);
        return new JsonResponse($response->toJsonResponse());
    }
}
