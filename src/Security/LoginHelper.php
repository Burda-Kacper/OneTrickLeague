<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class LoginHelper
{

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function setLastLogin(User $user): void
    {
        $user->setLastLogin(new DateTime('now'));
        $this->em->persist($user);
        $this->em->flush();
    }
}
