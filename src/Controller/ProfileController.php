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
        $user = $this->getUser();
        $section = $request->get('section');
        $response =  new JsonResponse([
            'success' => false,
            'data' => ProfileError::PROFILE_WRONG_SECTION
        ]);
        switch ($section) {
            case 'quiz':
                $response = new JsonResponse([
                    'success' => true,
                    'data' => $this->renderView('profile/section/_quiz.html.twig')
                ]);
                break;
            case 'profile':
                $availablePictures = $user->getAvailablePictures();
                $response = new JsonResponse([
                    'success' => true,
                    'data' => $this->renderView('profile/section/_profile.html.twig', [
                        'availablePictures' => $availablePictures
                    ])
                ]);
                break;
        }
        return $response;
    }

    public function picture(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $pictureId = $request->get("pictureId");
        $response = $this->profileService->setProfilePicture($user, intval($pictureId));
        return new JsonResponse($response->toJsonResponse());
    }
}
