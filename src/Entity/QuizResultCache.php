<?php

namespace App\Entity;

use App\Repository\QuizResultCacheRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizResultCacheRepository::class)
 */
class QuizResultCache
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="quizResultCache")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $finishedAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $averageScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $answerRightAmount;

    /**
     * @ORM\Column(type="integer")
     */
    private $answerWrongAmount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registered;

    public function __construct()
    {
        $this->registered = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFinishedAmount(): ?int
    {
        return $this->finishedAmount;
    }

    public function setFinishedAmount(int $finishedAmount): self
    {
        $this->finishedAmount = $finishedAmount;

        return $this;
    }

    public function getAverageScore(): ?float
    {
        return $this->averageScore;
    }

    public function setAverageScore(float $averageScore): self
    {
        $this->averageScore = $averageScore;

        return $this;
    }

    public function getAnswerRightAmount(): ?int
    {
        return $this->answerRightAmount;
    }

    public function setAnswerRightAmount(int $answerRightAmount): self
    {
        $this->answerRightAmount = $answerRightAmount;

        return $this;
    }

    public function getAnswerWrongAmount(): ?int
    {
        return $this->answerWrongAmount;
    }

    public function setAnswerWrongAmount(int $answerWrongAmount): self
    {
        $this->answerWrongAmount = $answerWrongAmount;

        return $this;
    }

    public function getRegistered(): ?\DateTimeInterface
    {
        return $this->registered;
    }

    public function setRegistered(\DateTimeInterface $registered): self
    {
        $this->registered = $registered;

        return $this;
    }
}
