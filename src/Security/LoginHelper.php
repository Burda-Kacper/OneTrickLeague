<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class LoginHelper
{

    private $em;

    public function __construct(
        EntityManagerInterface $em,
    ) {
        $this->em = $em;
    }

    public function setLastLogin(User $user): void
    {
        $user->setLastLogin(new DateTime('now'));
        $this->em->persist($user);
        $this->em->flush();
    }
}
