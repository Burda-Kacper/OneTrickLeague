<?php

namespace App\Administration\Controller;

use App\Administration\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function users(): Response
    {
        $users = $this->userService->getUsersByParams();
        return $this->render('administration/user/users.html.twig', [
            'users' => $users
        ]);
    }
}
