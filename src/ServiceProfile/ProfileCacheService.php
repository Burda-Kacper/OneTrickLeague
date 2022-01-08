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
    private $quizResultCacheRepo;
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

    public function clearQuizResultCache(User $user): void
    {
        $quizResultCache = $this->getUserQuizCache($user);
        if ($quizResultCache) {
            $this->em->remove($quizResultCache);
            $this->em->flush();
        }
    }

    public function rebuildQuizResultCache(User $user): void
    {
        $quizResultCache = new QuizResultCache;
        $quizResultCache->setUser($user);
        $quizesInfo = $this->quizRepository->getQuizCacheInfoForUser($user);

        if ($quizesInfo) {
            $quizFinishedAmount = $this->getQuizFinishedAmount($quizesInfo);
            $quizResultCache->setFinishedAmount($quizFinishedAmount);

            $quizUserAnswers = $this->getQuizUserAnswers($quizesInfo);

            $quizAnswerRightAmount = $this->getQuizAnswerRightAmount($quizUserAnswers);
            $quizResultCache->setAnswerRightAmount($quizAnswerRightAmount);

            $quizAnswerWrongAmount = count($quizUserAnswers) - $quizAnswerRightAmount;
            $quizResultCache->setAnswerWrongAmount($quizAnswerWrongAmount);

            $quizAverageScore = number_format($quizAnswerRightAmount / count($quizUserAnswers) * QuizQuestionService::QUESTIONS_IN_QUIZ, 1);
            $quizResultCache->setAverageScore($quizAverageScore);
        } else {
            $quizResultCache->setFinishedAmount(0);
            $quizResultCache->setAnswerRightAmount(0);
            $quizResultCache->setAnswerWrongAmount(0);
            $quizResultCache->setAverageScore(0);
        }

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

    public function getUserQuizCache(User $user): ?QuizResultCache
    {
        return $this->quizResultCacheRepo->findOneBy([
            'user' => $user
        ]);
    }
}
