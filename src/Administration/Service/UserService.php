<?php

namespace App\Administration\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{

    private $em;
    private $repo;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $repo
    ) {
        $this->repo = $repo;
        $this->em = $em;
    }

    public function getUsersByParams(array $criteria = [], array $orderBy = [], int $limit = null, int $offset = null): array
    {
        return $this->repo->findBy(
            $criteria,
            $orderBy,
            $limit,
            $offset
        );
    }
}
