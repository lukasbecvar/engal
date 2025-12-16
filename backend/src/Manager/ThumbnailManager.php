<?php

namespace App\Manager;

use Exception;
use App\Repository\MediaRepository;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Class ThumbnailManager
 *
 * ThumbnailManager class for managing media thumbnails
 *
 * @package App\Manager
 */
class ThumbnailManager
{
    private ErrorManager $errorManager;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;

    public function __construct(ErrorManager $errorManager, StorageManager $storageManager, MediaRepository $mediaRepository)
    {
        $this->errorManager = $errorManager;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Retrieves the thumbnail of a media resource
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media resource
     *
     * @return mixed The encoded image object representing the thumbnail
     */
    public function getMediaThumbnail(int $userId, string $token): mixed
    {
        // return cached thumbnail if already stored
        $existing = $this->getMediaExistThumbnail($userId, $token);
        if ($existing !== null) {
            return $existing;
        }

        // get media type
        $mediaType = $this->storageManager->getMediaType($token);

        // select media to resize
        if (str_contains($mediaType, 'image')) {
            $mediaFile = $this->storageManager->getMediaFile($userId, $token);
        } else {
            $mediaFile = $this->getVideoThumbnail($userId, $token);
        }

        return $this->storeThumbnail($mediaFile, $userId, $token);
    }

    /**
     * Generates a thumbnail for a video media file associated with the given user ID and token
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the video media file
     *
     * @return string|null The content of the generated thumbnail, or null if not found
     */
    public function getVideoThumbnail(int $userId, string $token): ?string
    {
        // get video file
        $mediaFile = $this->storageManager->getMediaFile($userId, $token);

        // generate temporary thumbnail path
        $thumbnailPath = sys_get_temp_dir() . '/engal_thumb_' . $token . '.jpg';

        // create thumbnail if missing
        if (!file_exists($thumbnailPath)) {
            // get video duration
            $duration = shell_exec('ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($mediaFile));
            $duration = floatval($duration);

            // generate thumbnail (middle time)
            exec('ffmpeg -ss ' . ($duration / 2.1) . ' -i ' . escapeshellarg($mediaFile) . ' -vframes 1 ' . escapeshellarg($thumbnailPath));

            // if ffmpeg failed, create a simple placeholder thumbnail
            if (!file_exists($thumbnailPath)) {
                $manager = new ImageManager(new Driver());
                $placeholder = $manager->create(320, 180)->fill('#000000');
                $placeholder->encodeByPath($thumbnailPath, type: 'jpg');
            }
        }

        return $thumbnailPath;
    }

    /**
     * Store a thumbnail of the given media file and return the encoded thumbnail image
     *
     * @param string $mediaFile The path to the original media file
     * @param int $userId The user ID associated with the media
     * @param string $token The unique token identifier for the thumbnail
     *
     * @return mixed The encoded thumbnail image object, or null if the thumbnail couldn't be created
     */
    public function storeThumbnail(string $mediaFile, int $userId, string $token): mixed
    {
        // read media file
        $mediaFile = file_get_contents($mediaFile);

        try {
            // init Intervention manager
            $manager = new ImageManager(new Driver());

            // read media file
            $image = $manager->read($mediaFile);
        } catch (\Throwable) {
            // fallback to raw media content if thumbnail generation fails
            return $this->storageManager->getMediaContent($userId, $token);
        }

        // get actual image property
        $width = $image->width();
        $height = $image->height();

        // check if image can by resized
        if ($width < 700 && $height < 1300) {
            // store original as thumbnail to avoid regenerating on every request
            $this->storageManager->storeThumbnailContent($userId, $token, $mediaFile);
            return $mediaFile;
        }

        if ($width > 2000 && $height > 3000) {
            $newWidth = $width / 3.4;
            $newHeight = $height / 3.4;
        } else {
            $newWidth = $width / 2.1;
            $newHeight = $height / 2.1;
        }

        // resize media file
        $image->resize((int) $newWidth, (int) $newHeight);

        // encode thumbnail image
        $image = $image->encode();

        // save image thumbnail to storage cache (S3/MinIO)
        $this->storageManager->storeThumbnailContent($userId, $token, $image);

        // return encoded image object
        return $image;
    }

    /**
     * Retrieve an existing media thumbnail image
     *
     * @param int $userId The user ID associated with the media
     * @param string $token The unique token identifier for the thumbnail
     *
     * @return string|null The content of the thumbnail image file, or null if the thumbnail doesn't exist
     */
    public function getMediaExistThumbnail(int $userId, string $token): ?string
    {
        return $this->storageManager->getThumbnailContent($userId, $token);
    }

    /**
     * Preloads thumbnails for all media files in the system
     *
     * Retrieves all media files from the repository and iterates through each one
     * For each media file, it retrieves the associated media data and checks if it's an image or a video
     * If it's a video, it fetches the thumbnail. Then, it checks if a thumbnail already exists for the media file
     * If not, it stores the thumbnail
     *
     * @param string $referer The referer indicating where the command originated from
     * @param int $userId The user is for preload process
     * @param string|null $galleryName The gallery name for preload process
     *
     * @throws Exception If an error occurs during the thumbnail preloading process
     *
     * @return void
     */
    public function preloadAllThumbnails(?string $referer = null, ?int $userId = null, ?string $galleryName = null): void
    {
        // select media list
        if ($userId == null) {
            $mediaList = $this->mediaRepository->findAllMedia();
        } else {
            // check if gallery name defined
            if ($galleryName == null) {
                $mediaList = $this->mediaRepository->findAllMediaByOwnerId($userId);
            } else {
                $mediaList = $this->mediaRepository->findAllMediaByGalleryName($userId, $galleryName);
            }
        }

        try {
            foreach ($mediaList as $media) {
                // get media data
                $userId = $media['owner_id'];
                $token = $media['token'];

                // get media file
                $mediaFile = $this->storageManager->getMediaFile($userId, $token);

                // check if media is video
                if (!str_contains($media['type'], 'image')) {
                    // get video thumbnail
                    $mediaFile = $this->getVideoThumbnail($userId, $token);
                }

                // check if thumbnail is already exist
                if ($this->getMediaExistThumbnail($userId, $token) == null) {
                    // check if media file is exist
                    if ($mediaFile != null) {
                        // store thumbnail
                        $this->storeThumbnail($mediaFile, $userId, $token);
                    }
                }

                // check command referer (print progress to console outputs)
                if ($referer == 'console_command' && ($_ENV['APP_ENV'] != 'test')) {
                    dump('Thumbnail: (' . $media['id'] . '/' . count($mediaList) . ') -> ' . $token . ' preload completed!');
                }
            }
        } catch (Exception $e) {
            $this->errorManager->handleError('error to preload thumbnails: ' . $e->getMessage(), 500);
        }
    }
}
