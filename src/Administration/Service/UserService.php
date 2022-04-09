<?php

namespace App\Administration\Service;

use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $repo;

    /**
     * @param UserRepository $repo
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getUsersByParams(array $criteria, array $orderBy, int $limit, int $offset): array
    {
        return $this->repo->getUsersByParams(
            $criteria,
            $orderBy,
            $limit,
            $offset
        );
    }
}
