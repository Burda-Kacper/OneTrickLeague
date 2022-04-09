<?php

namespace App\ServiceQuiz;

use App\DataStructure\MainResponse;
use App\Entity\Quiz;
use App\Entity\QuizAnswer;
use App\Entity\QuizSaved;
use App\Repository\QuizSavedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\QuizMessage;
use App\ServiceCommon\TokenService;
use Exception;

class QuizSavedService
{
    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var QuizSavedRepository $repo
     */
    private QuizSavedRepository $repo;

    /**
     * @param EntityManagerInterface $em
     * @param QuizSavedRepository $repo
     */
    public function __construct(EntityManagerInterface $em, QuizSavedRepository $repo)
    {
        $this->repo = $repo;
        $this->em = $em;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generateToken(): string
    {
        $token = TokenService::generateToken();
        $takenToken = $this->getQuizSavedByToken($token);

        if ($takenToken) {
            return $this->generateToken();
        }

        return $token;
    }

    /**
     * @param string $token
     *
     * @return QuizSaved|null
     */
    public function getQuizSavedByToken(string $token): ?QuizSaved
    {
        return $this->repo->findOneBy([
            'token' => $token
        ]);
    }

    /**
     * @param Quiz $quiz
     * @param QuizSaved|null $quizSaved
     * @param array $quizUserAnswers
     *
     * @return string
     *
     * @throws Exception
     */
    public function assignQuizSavedToQuiz(Quiz $quiz, ?QuizSaved $quizSaved, array $quizUserAnswers): string
    {
        if (!$quizSaved) {
            $token = $this->generateToken();
            $quizSaved = new QuizSaved;
            $quizSaved->setToken($token);
            $quizSaved->setOwner($quiz->getUser());

            foreach ($quizUserAnswers as $answer) {
                $quizSaved->addQuestion($answer->getQuestion());
            }

            $this->em->persist($quizSaved);
            $this->em->flush();
        }

        $quiz->setQuizSaved($quizSaved);
        $this->em->persist($quiz);
        $this->em->flush();

        return $quizSaved->getToken();
    }

    /**
     * @param string $quizSavedToken
     *
     * @return MainResponse
     */
    public function getQuestionsForQuizSavedToken(string $quizSavedToken): MainResponse
    {
        $quizSaved = $this->getQuizSavedByToken($quizSavedToken);

        if (!$quizSaved) {
            return new MainResponse(false, QuizMessage::QUIZ_SAVED_NOT_FOUND);
        }

        $questions = $quizSaved->getQuestions();
        
        return new MainResponse(true, $questions->toArray());
    }
}
