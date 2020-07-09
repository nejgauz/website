<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AlbumRepository::class)
 */
class Album
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dt_create;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Length(
     *     min=3,
     *     max=100,
     *     )
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "'{{ value }}' - некорректный электронный адрес."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * дата и время изменения альбома
     */
    private $dt_change;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="album", orphanRemoval=true)
     */
    private $photos;

    public function __construct()
    {
        $this->dt_create = new \DateTime();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDtCreate(): ?\DateTimeInterface
    {
        return $this->dt_create;
    }

    public function setDtCreate(\DateTimeInterface $dt_create): self
    {
        $this->dt_create = $dt_create;

        return $this;
    }

    public function getDtChange(): ?\DateTimeInterface
    {
        return $this->dt_change;
    }

    public function setDtChange(\DateTimeInterface $dt_change): self
    {
        $this->dt_change = $dt_change;

        return $this;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setAlbum($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getAlbum() === $this) {
                $photo->setAlbum(null);
            }
        }

        return $this;
    }
}
