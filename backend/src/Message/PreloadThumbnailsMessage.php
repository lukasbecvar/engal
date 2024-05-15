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
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Gets the path associated with the message.
     *
     * @return string The path to preload thumbnails for.
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
