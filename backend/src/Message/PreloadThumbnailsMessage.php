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

    public function __construct(int $ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * Gets the owner id associated with the message.
     *
     * @return int The owner id for preload.
     */
    public function getOwnerId(): string
    {
        return $this->ownerId;
    }
}
