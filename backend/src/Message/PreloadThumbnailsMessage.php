<?php

namespace App\Message;

/**
 * Class PreloadThumbnailsMessage
 *
 * Represents a message to preload thumbnails.
 *
 * @package App\Message
 */
class PreloadThumbnailsMessage
{
    private int $ownerId;
    private string|null $galleryName;

    public function __construct(int $ownerId, ?string $galleryName)
    {
        $this->ownerId = $ownerId;
        $this->galleryName = $galleryName;
    }

    /**
     * Gets the owner id to preload.
     *
     * @return int The owner id for preload.
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * Gets the gallery name to preload.
     *
     * @return string The gallery name for preload.
     */
    public function getGalleryName(): ?string
    {
        return $this->galleryName;
    }
}
