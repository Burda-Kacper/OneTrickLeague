<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Quiz::class, mappedBy="user")
     */
    private $quizzes;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilePicture::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $profilePicture;

    /**
     * @ORM\ManyToMany(targetEntity=ProfilePicture::class, mappedBy="userHasPictures")
     */
    private $availablePictures;

    /**
     * @ORM\OneToMany(targetEntity=QuizResultCache::class, mappedBy="user")
     */
    private $quizResultCache;

    /**
     * @ORM\OneToMany(targetEntity=QuizSaved::class, mappedBy="owner", orphanRemoval=true)
     */
    private $quizSaveds;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublic = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAllowedToAddQuizQuestions = true;

    /**
     * @ORM\OneToMany(targetEntity=QuizQuestion::class, mappedBy="owner", orphanRemoval=true)
     */
    private $quizQuestions;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $registered;

    public function __construct()
    {
        $this->quizzes = new ArrayCollection();
        $this->availablePictures = new ArrayCollection();
        $this->quizSaveds = new ArrayCollection();
        $this->quizQuestions = new ArrayCollection();
        $this->registered = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $quiz->setUser($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz)) {
            // set the owning side to null (unless already changed)
            if ($quiz->getUser() === $this) {
                $quiz->setUser(null);
            }
        }

        return $this;
    }

    public function getProfilePicture(): ?ProfilePicture
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?ProfilePicture $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * @return Collection|ProfilePicture[]
     */
    public function getAvailablePictures(): Collection
    {
        return $this->availablePictures;
    }

    public function addAvailablePicture(ProfilePicture $availablePicture): self
    {
        if (!$this->availablePictures->contains($availablePicture)) {
            $this->availablePictures[] = $availablePicture;
            $availablePicture->addUserHasPicture($this);
        }

        return $this;
    }

    public function removeAvailablePicture(ProfilePicture $availablePicture): self
    {
        if ($this->availablePictures->removeElement($availablePicture)) {
            $availablePicture->removeUserHasPicture($this);
        }

        return $this;
    }

    public function getQuizResultCache(): ?QuizResultCache
    {
        return $this->quizResultCache;
    }

    public function setQuizResultCache(QuizResultCache $quizResultCache): self
    {
        // set the owning side of the relation if necessary
        if ($quizResultCache->getUser() !== $this) {
            $quizResultCache->setUser($this);
        }

        $this->quizResultCache = $quizResultCache;

        return $this;
    }

    /**
     * @return Collection|QuizSaved[]
     */
    public function getQuizSaveds(): Collection
    {
        return $this->quizSaveds;
    }

    public function addQuizSaved(QuizSaved $quizSaved): self
    {
        if (!$this->quizSaveds->contains($quizSaved)) {
            $this->quizSaveds[] = $quizSaved;
            $quizSaved->setOwner($this);
        }

        return $this;
    }

    public function removeQuizSaved(QuizSaved $quizSaved): self
    {
        if ($this->quizSaveds->removeElement($quizSaved)) {
            // set the owning side to null (unless already changed)
            if ($quizSaved->getOwner() === $this) {
                $quizSaved->setOwner(null);
            }
        }

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getIsAllowedToAddQuizQuestions(): ?bool
    {
        return $this->isAllowedToAddQuizQuestions;
    }

    public function setIsAllowedToAddQuizQuestions(bool $isAllowedToAddQuizQuestions): self
    {
        $this->isAllowedToAddQuizQuestions = $isAllowedToAddQuizQuestions;

        return $this;
    }

    /**
     * @return Collection|QuizQuestion[]
     */
    public function getQuizQuestions(): Collection
    {
        return $this->quizQuestions;
    }

    public function addQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if (!$this->quizQuestions->contains($quizQuestion)) {
            $this->quizQuestions[] = $quizQuestion;
            $quizQuestion->setOwner($this);
        }

        return $this;
    }

    public function removeQuizQuestion(QuizQuestion $quizQuestion): self
    {
        if ($this->quizQuestions->removeElement($quizQuestion)) {
            // set the owning side to null (unless already changed)
            if ($quizQuestion->getOwner() === $this) {
                $quizQuestion->setOwner(null);
            }
        }

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getRegistered(): ?\DateTimeInterface
    {
        return $this->registered;
    }

    public function setRegistered(?\DateTimeInterface $registered): self
    {
        $this->registered = $registered;

        return $this;
    }
}
