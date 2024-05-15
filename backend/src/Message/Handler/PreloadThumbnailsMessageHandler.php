<?php

namespace App\Message\Handler;

use App\Manager\StorageManager;
use App\Message\PreloadThumbnailsMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Class PreloadThumbnailsMessageHandler
 *
 * Message handler responsible for handling PreloadThumbnailsMessage.
 * This handler is triggered when a PreloadThumbnailsMessage is dispatched.
 *
 * @package App\Message\Handler
 */
#[AsMessageHandler]
class PreloadThumbnailsMessageHandler
{
    private StorageManager $storageManager;

    public function __construct(StorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * Handles the PreloadThumbnailsMessage.
     *
     * This method is invoked when a PreloadThumbnailsMessage is dispatched.
     *
     * @param PreloadThumbnailsMessage $message The PreloadThumbnailsMessage to handle.
     * @return void
     */
    public function __invoke(PreloadThumbnailsMessage $message)
    {
        // Preload all thumbnails using the storage manager
        $this->storageManager->preloadAllThumbnails();
    }
}
