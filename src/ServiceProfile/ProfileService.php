<?php

namespace App\ServiceProfile;

use App\DataStructure\MainResponse;
use App\Entity\QuizResultCache;
use App\Entity\User;
use App\Message\ProfileMessage;
use App\Repository\ProfilePictureRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ProfileService
{

    private $em;
    private $repo;
    private $profilePictureRepo;
    private $profileCacheService;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $repo,
        ProfilePictureRepository $profilePictureRepo,
        ProfileCacheService $profileCacheService
    ) {
        $this->repo = $repo;
        $this->em = $em;
        $this->profilePictureRepo = $profilePictureRepo;
        $this->profileCacheService = $profileCacheService;
    }

    public function setProfilePicture(User $user, ?int $pictureId): MainResponse
    {
        $picture = $this->profilePictureRepo->findOneBy([
            'id' => $pictureId
        ]);
        if (!$picture) {
            return new MainResponse(false, ProfileMessage::PROFILE_WRONG_PICTURE);
        }

        $availablePictures = $user->getAvailablePictures();
        if (!in_array($picture, $availablePictures->toArray())) {
            return new MainResponse(false, ProfileMessage::PROFILE_UNAVAILABLE_PICTURE);
        }
        $user->setProfilePicture($picture);
        $this->em->persist($user);
        $this->em->flush();

        return new MainResponse(true, $picture->getImage());
    }

    public function refreshResultCache(User $user): void
    {
        $this->profileCacheService->clearQuizResultCache($user);
        $this->profileCacheService->rebuildQuizResultCache($user);
    }

    public function getUserQuizCache(User $user): ?QuizResultCache
    {
        return $this->profileCacheService->getUserQuizCache($user);
    }
}
