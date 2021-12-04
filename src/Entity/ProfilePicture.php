<?php

namespace App\Entity;

use App\Repository\ProfilePictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfilePictureRepository::class)
 */
class ProfilePicture
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
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="availablePictures")
     */
    private $userHasPictures;

    public function __construct()
    {
        $this->userHasPictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserHasPictures(): Collection
    {
        return $this->userHasPictures;
    }

    public function addUserHasPicture(User $userHasPicture): self
    {
        if (!$this->userHasPictures->contains($userHasPicture)) {
            $this->userHasPictures[] = $userHasPicture;
        }

        return $this;
    }

    public function removeUserHasPicture(User $userHasPicture): self
    {
        $this->userHasPictures->removeElement($userHasPicture);

        return $this;
    }
}
