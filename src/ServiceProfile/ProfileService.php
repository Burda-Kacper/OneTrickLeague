<?php

namespace App\ServiceProfile;

use App\DataStructure\MainResponse;
use App\Entity\User;
use App\Error\ProfileError;
use App\Repository\ProfilePictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProfileService
{

    private $em;
    private $repo;
    private $profilePictureRepo;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $repo,
        ProfilePictureRepository $profilePictureRepo
    ) {
        $this->repo = $repo;
        $this->em = $em;
        $this->profilePictureRepo = $profilePictureRepo;
    }

    public function setProfilePicture(User $user, ?int $pictureId): MainResponse
    {
        $picture = $this->profilePictureRepo->findOneBy([
            'id' => $pictureId
        ]);
        if (!$picture) {
            return new MainResponse(false, ProfileError::PROFILE_WRONG_PICTURE);
        }

        $availablePictures = $user->getAvailablePictures();
        if (!in_array($picture, $availablePictures->toArray())) {
            return new MainResponse(false, ProfileError::PROFILE_UNAVAILABLE_PICTURE);
        }
        $user->setProfilePicture($picture);
        $this->em->persist($user);
        $this->em->flush();

        return new MainResponse(true, $picture->getImage());
    }
}
