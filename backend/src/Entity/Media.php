<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;

/**
 * Class Media
 *
 * The Media database table mapping entity
 *
 * @package App\Entity
 */
#[ORM\Table(name: 'media')]
#[ORM\Index(name: 'media_token_idx', columns: ['token'])]
#[ORM\Index(name: 'media_owner_id_idx', columns: ['owner_id'])]
#[ORM\Index(name: 'media_gallery_name_idx', columns: ['gallery_name'])]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $gallery_name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $length = null;

    #[ORM\Column]
    private ?int $owner_id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private ?string $upload_time = null;

    #[ORM\Column(length: 255)]
    private ?string $last_edit_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGalleryName(): ?string
    {
        return $this->gallery_name;
    }

    public function setGalleryName(string $gallery_name): static
    {
        $this->gallery_name = $gallery_name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    public function setOwnerId(int $owner_id): static
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getUploadTime(): ?string
    {
        return $this->upload_time;
    }

    public function setUploadTime(string $upload_time): static
    {
        $this->upload_time = $upload_time;

        return $this;
    }

    public function getLastEditTime(): ?string
    {
        return $this->last_edit_time;
    }

    public function setLastEditTime(string $last_edit_time): static
    {
        $this->last_edit_time = $last_edit_time;

        return $this;
    }
}
