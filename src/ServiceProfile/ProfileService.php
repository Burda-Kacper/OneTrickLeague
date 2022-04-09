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

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var ProfilePictureRepository $profilePictureRepo
     */
    private ProfilePictureRepository $profilePictureRepo;

    /**
     * @var ProfileCacheService $profileCacheService
     */
    private ProfileCacheService $profileCacheService;

    /**
     * @var UserPasswordHasherInterface $hasher
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * @param EntityManagerInterface $em
     * @param ProfilePictureRepository $profilePictureRepo
     * @param ProfileCacheService $profileCacheService
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(
        EntityManagerInterface      $em,
        ProfilePictureRepository    $profilePictureRepo,
        ProfileCacheService         $profileCacheService,
        UserPasswordHasherInterface $hasher
    )
    {
        $this->em = $em;
        $this->profilePictureRepo = $profilePictureRepo;
        $this->profileCacheService = $profileCacheService;
        $this->hasher = $hasher;
    }

    /**
     * @param User $user
     * @param int|null $pictureId
     *
     * @return MainResponse
     */
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

    /**
     * @param User|null $user
     *
     * @return void
     */
    public function refreshResultCache(?User $user): void
    {
        if ($user) {
            $this->profileCacheService->clearQuizResultCache($user);
            $this->profileCacheService->rebuildQuizResultCache($user);
        }
    }

    /**
     * @param User $user
     *
     * @return QuizResultCache|null
     */
    public function getUserQuizCache(User $user): ?QuizResultCache
    {
        return $this->profileCacheService->getUserQuizCache($user);
    }

    /**
     * @param User $user
     * @param array $passwords
     *
     * @return MainResponse
     */
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
