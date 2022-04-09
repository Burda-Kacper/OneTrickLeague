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

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var QuizResultCacheRepository $quizResultCacheRepo
     */
    private QuizResultCacheRepository $quizResultCacheRepo;

    /**
     * @var QuizRepository $quizRepository
     */
    private QuizRepository $quizRepository;

    /**
     * @param EntityManagerInterface $em
     * @param QuizResultCacheRepository $quizResultCacheRepository
     * @param QuizRepository $quizRepository
     */
    public function __construct(
        EntityManagerInterface    $em,
        QuizResultCacheRepository $quizResultCacheRepository,
        QuizRepository            $quizRepository
    )
    {
        $this->quizResultCacheRepo = $quizResultCacheRepository;
        $this->em = $em;
        $this->quizRepository = $quizRepository;
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function clearQuizResultCache(User $user): void
    {
        $quizResultCache = $this->getUserQuizCache($user);

        if ($quizResultCache) {
            $this->em->remove($quizResultCache);
            $this->em->flush();
        }
    }

    /**
     * @param User $user
     *
     * @return void
     */
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

    /**
     * @param array $quizesInfo
     *
     * @return int
     */
    private function getQuizFinishedAmount(array $quizesInfo): int
    {
        return count($quizesInfo);
    }

    /**
     * @param array $quizesInfo
     *
     * @return array
     */
    private function getQuizUserAnswers(array $quizesInfo): array
    {
        $userAnswers = [];

        foreach ($quizesInfo as $quizInfo) {
            $userAnswers = array_merge($userAnswers, $quizInfo->getUserAnswers()->toArray());
        }

        return $userAnswers;
    }

    /**
     * @param array $quizUserAnswers
     *
     * @return int
     */
    private function getQuizAnswerRightAmount(array $quizUserAnswers): int
    {
        $rightAnswers = 0;

        foreach ($quizUserAnswers as $qua) {
            if (!$qua->getAnswer()) {
                continue;
            }

            if ($qua->getAnswer()->getIsCorrect()) {
                $rightAnswers++;
            }
        }

        return $rightAnswers;
    }

    /**
     * @param User $user
     *
     * @return QuizResultCache|null
     */
    public function getUserQuizCache(User $user): ?QuizResultCache
    {
        return $this->quizResultCacheRepo->findOneBy([
            'user' => $user
        ]);
    }
}
