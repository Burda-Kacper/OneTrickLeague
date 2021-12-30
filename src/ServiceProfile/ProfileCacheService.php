<?php

namespace App\ServiceProfile;

use App\DataStructure\MainResponse;
use App\Entity\QuizResultCache;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\QuizResultCacheRepository;
use App\ServiceQuiz\QuizQuestionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ProfileCacheService
{
    //ETODO: Move this into the backend
    const CACHE_LIFETIME = "-10 minutes";

    private $em;
    private $quizResultCacheRepository;
    private $quizRepository;


    public function __construct(
        EntityManagerInterface $em,
        QuizResultCacheRepository $quizResultCacheRepository,
        QuizRepository $quizRepository
    ) {
        $this->quizResultCacheRepo = $quizResultCacheRepository;
        $this->em = $em;
        $this->quizRepository = $quizRepository;
    }

    public function clearQuizResultCache(User $user): bool
    {
        $quizResultCache = $this->getUserQuizCache($user);
        if ($quizResultCache) {
            $cacheTimeout = (new DateTime('now'))->modify($this::CACHE_LIFETIME);
            if ($quizResultCache->getRegistered() < $cacheTimeout) {
                $this->em->remove($quizResultCache);
                $this->em->flush();
                return true;
            }
            return false;
        }
        return true;
    }

    public function rebuildQuizResultCache(User $user): void
    {
        //ETODO: Consider when user has 0 activity
        $quizResultCache = new QuizResultCache;
        $quizResultCache->setUser($user);
        $quizesInfo = $this->quizRepository->getQuizCacheInfoForUser($user);

        $quizFinishedAmount = $this->getQuizFinishedAmount($quizesInfo);
        $quizResultCache->setFinishedAmount($quizFinishedAmount);

        $quizUserAnswers = $this->getQuizUserAnswers($quizesInfo);

        $quizAnswerRightAmount = $this->getQuizAnswerRightAmount($quizUserAnswers);
        $quizResultCache->setAnswerRightAmount($quizAnswerRightAmount);

        $quizAnswerWrongAmount = count($quizUserAnswers) - $quizAnswerRightAmount;
        $quizResultCache->setAnswerWrongAmount($quizAnswerWrongAmount);

        $quizAverageScore = number_format($quizAnswerRightAmount / count($quizUserAnswers) * QuizQuestionService::QUESTIONS_IN_QUIZ, 1);
        $quizResultCache->setAverageScore($quizAverageScore);

        $this->em->persist($quizResultCache);
        $this->em->flush();
    }

    private function getQuizFinishedAmount(array $quizesInfo): int
    {
        return count($quizesInfo);
    }

    private function getQuizUserAnswers(array $quizesInfo): array
    {
        $userAnswers = [];
        foreach ($quizesInfo as $quizInfo) {
            $userAnswers = array_merge($userAnswers, $quizInfo->getUserAnswers()->toArray());
        }
        return $userAnswers;
    }

    private function getQuizAnswerRightAmount(array $quizUserAnswers): int
    {
        $rightAnswers = 0;
        foreach ($quizUserAnswers as $qua) {
            if ($qua->getAnswer()->getIsCorrect()) {
                $rightAnswers++;
            }
        }
        return $rightAnswers;
    }

    public function getUserQuizCache(User $user)
    {
        return $this->quizResultCacheRepo->findOneBy([
            'user' => $user
        ]);
    }
}