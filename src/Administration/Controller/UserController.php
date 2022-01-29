<?php

namespace App\Administration\Controller;

use App\Administration\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('administration/user/users.html.twig');
    }

    public function usersEntries(Request $request): JsonResponse
    {
        $username = $request->get('username');
        $amount = $request->get('amount');
        $page = $request->get('page');
        $sort = [
            'field' => $request->get("sort")['field'],
            'order' => $request->get("sort")['order']
        ];
        $users = $this->userService->getUsersByParams([
            'username' =>  [
                'clausule' => 'LIKE',
                'value' => "%" . $username . "%"
            ]
        ], [
            'field' => $sort['field'],
            'order' => $sort['order']
        ], $amount, $page);

        return new JsonResponse([
            'success' => true,
            'template' => $this->renderView('administration/user/_usersTable.html.twig', [
                'users' => $users
            ])
        ]);
    }
}
