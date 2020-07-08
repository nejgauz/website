<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $album_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dt_create;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_path;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlbumId(): ?Album
    {
        return $this->album_id;
    }

    public function setAlbumId(?Album $album_id): self
    {
        $this->album_id = $album_id;

        return $this;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(string $image_path): self
    {
        $this->image_path = $image_path;

        return $this;
    }
}
