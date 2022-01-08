<?php

namespace App\Entity;

use App\Repository\QuizSavedRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizSavedRepository::class)
 */
class QuizSaved
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToMany(targetEntity=QuizQuestion::class, mappedBy="quizSaved")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity=Quiz::class, mappedBy="quizSaved", orphanRemoval=true)
     */
    private $quizzes;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->created = new DateTime('now');
        $this->quizzes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection|QuizQuestion[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(QuizQuestion $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->addQuizSaved($this);
        }

        return $this;
    }

    public function removeQuestion(QuizQuestion $question): self
    {
        if ($this->questions->removeElement($question)) {
            $question->removeQuizSaved($this);
        }

        return $this;
    }

    /**
     * @return Collection|Quiz[]
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes[] = $quiz;
            $quiz->setQuizSaved($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getQuizSaved() === $this) {
                $quiz->setQuizSaved(null);
            }
        }

        return $this;
    }
}
