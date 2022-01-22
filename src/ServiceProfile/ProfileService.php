<?php

namespace App\ServiceProfile;

use App\DataStructure\MainResponse;
use App\Entity\QuizResultCache;
use App\Entity\User;
use App\Message\ProfileMessage;
use App\Repository\ProfilePictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileService
{
    const PASSWORD_MIN_LENGTH = 8;

    private $em;
    private $repo;
    private $profilePictureRepo;
    private $profileCacheService;
    private $hasher;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $repo,
        ProfilePictureRepository $profilePictureRepo,
        ProfileCacheService $profileCacheService,
        UserPasswordHasherInterface $hasher
    ) {
        $this->repo = $repo;
        $this->em = $em;
        $this->profilePictureRepo = $profilePictureRepo;
        $this->profileCacheService = $profileCacheService;
        $this->hasher = $hasher;
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

    public function refreshResultCache(?User $user): void
    {
        if ($user) {
            $this->profileCacheService->clearQuizResultCache($user);
            $this->profileCacheService->rebuildQuizResultCache($user);
        }
    }

    public function getUserQuizCache(User $user): ?QuizResultCache
    {
        return $this->profileCacheService->getUserQuizCache($user);
    }

    public function changePassword(User $user, array $passwords): MainResponse
    {
        if (!$this->hasher->isPasswordValid($user, $passwords['old'])) {
            return new MainResponse(false, ProfileMessage::PROFILE_PASSWORD_OLD_INCORRECT);
        }

        if (strlen($passwords['new']) < self::PASSWORD_MIN_LENGTH) {
            return new MainResponse(false, ProfileMessage::PROFILE_PASSWORD_NEW_SHORT);
        }

        if ($passwords['new'] !== $passwords['repeat']) {
            return new MainResponse(false, ProfileMessage::PROFILE_PASSWORD_REPEAT_INCORRECT);
        }

        $hashedPassword = $this->hasher->hashPassword($user, $passwords['new']);
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();

        return new MainResponse(true, ProfileMessage::PROFILE_PASSWORD_SAVED);
    }
}
