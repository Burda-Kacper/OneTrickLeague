<?php

namespace App\Entity;

use App\Repository\QuizUserAnsweredRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizUserAnsweredRepository::class)
 */
class QuizUserAnswered
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="userAnswers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quiz;

    /**
     * @ORM\ManyToOne(targetEntity=QuizAnswer::class)
     */
    private $answer = null;

    /**
     * @ORM\ManyToOne(targetEntity=QuizQuestion::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getAnswer(): ?QuizAnswer
    {
        return $this->answer;
    }

    public function setAnswer(?QuizAnswer $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getQuestion(): ?QuizQuestion
    {
        return $this->question;
    }

    public function setQuestion(?QuizQuestion $question): self
    {
        $this->question = $question;

        return $this;
    }
}
