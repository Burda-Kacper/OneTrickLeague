<?php

namespace App\Entity;

use App\Repository\QuizQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizQuestionRepository::class)
 */
class QuizQuestion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=QuizAnswer::class, mappedBy="question")
     */
    private $answers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = false;

    /**
     * @ORM\ManyToMany(targetEntity=QuizSaved::class, inversedBy="questions")
     */
    private $quizSaved;

    public function __construct()
    {
        $this->quiz = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->quizSaved = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection|QuizAnswer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(QuizAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(QuizAnswer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|QuizSaved[]
     */
    public function getQuizSaved(): Collection
    {
        return $this->quizSaved;
    }

    public function addQuizSaved(QuizSaved $quizSaved): self
    {
        if (!$this->quizSaved->contains($quizSaved)) {
            $this->quizSaved[] = $quizSaved;
        }

        return $this;
    }

    public function removeQuizSaved(QuizSaved $quizSaved): self
    {
        $this->quizSaved->removeElement($quizSaved);

        return $this;
    }
}
