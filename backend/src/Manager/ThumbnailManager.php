<?php

namespace App\Manager;

use App\Util\SecurityUtil;
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
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private StorageManager $storageManager;
    private MediaRepository $mediaRepository;

    public function __construct(SecurityUtil $securityUtil, ErrorManager $errorManager, StorageManager $storageManager, MediaRepository $mediaRepository)
    {
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->storageManager = $storageManager;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Retrieves the thumbnail of a media resource.
     *
     * @param int $userId The ID of the user.
     * @param string $token The token associated with the media resource.
     *
     * @return mixed The encoded image object representing the thumbnail.
     */
    public function getMediaThumbnail(int $userId, string $token): mixed
    {
        // get thumbnail cache if exist
        $existedThumbnail = $this->getMediaExistThumbnail($userId, $token);

        // return thumbnail if is already exist
        if ($existedThumbnail != null) {
            return $existedThumbnail;
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
     * Generates a thumbnail for a video media file associated with the given user ID and token.
     *
     * @param int $userId The ID of the user.
     * @param string $token The token associated with the video media file.
     *
     * @return string|null The content of the generated thumbnail, or null if not found.
     */
    public function getVideoThumbnail(int $userId, string $token): ?string
    {
        if ($_ENV['STORAGE_ENCRYPTION'] == 'true') {
            return null;
        }

        // get video file
        $mediaFile = $this->storageManager->getMediaFile($userId, $token);

        // build video thumnail file path
        $thumbnailDirectory = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/';

        // create thumbnail directory
        if (!file_exists($thumbnailDirectory)) {
            mkdir($thumbnailDirectory, 0777, true);
        }

        // create file path
        if (!file_exists($thumbnailDirectory . $token . '.jpg')) {
            // get video duration
            $duration = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $mediaFile");
            $duration = floatval($duration);

            // generate thumbnail (middle time)
            exec('ffmpeg -ss ' . ($duration / 2.1) . ' -i ' . $mediaFile . ' -vframes 1 ' . $thumbnailDirectory . $token . '.jpg');
        }

        return file_get_contents($thumbnailDirectory . $token . '.jpg');
    }

    /**
     * Store a thumbnail of the given media file and return the encoded thumbnail image.
     *
     * @param string $mediaFile The path to the original media file.
     * @param int $userId The user ID associated with the media.
     * @param string $token The unique token identifier for the thumbnail.
     *
     * @return mixed The encoded thumbnail image object, or null if the thumbnail couldn't be created.
     */
    public function storeThumbnail(string $mediaFile, int $userId, string $token): mixed
    {
        // decrypt media file
        $mediaFile = file_get_contents($mediaFile);
        $mediaFile = $this->securityUtil->decryptAES($mediaFile);

        // init Intervention manager
        $manager = new ImageManager(new Driver());

        // read media file
        $image = $manager->read($mediaFile);

        // get actual image property
        $width = $image->width();
        $height = $image->height();

        // check if image can by resized
        if ($width < 700 && $height < 1300) {
            return $this->storageManager->getMediaContent($userId, $token);
        }

        // thumbnail directory path
        $thumbnailDirectory = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/';

        // create thumbnail directory
        if (!file_exists($thumbnailDirectory)) {
            mkdir($thumbnailDirectory, 0777, true);
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

        // build thumbnail image pathern
        $thumbnailPathern = $thumbnailDirectory . $token . '.jpg';

        // encode image
        $image = $this->securityUtil->encryptAES($image);

        // save image thumbnail to storage cache
        file_put_contents($thumbnailPathern, $image);

        // return encoded image object
        return $image;
    }

    /**
     * Retrieve an existing media thumbnail image.
     *
     * @param int $userId The user ID associated with the media.
     * @param string $token The unique token identifier for the thumbnail.
     *
     * @return string|null The content of the thumbnail image file, or null if the thumbnail doesn't exist.
     */
    public function getMediaExistThumbnail(int $userId, string $token): ?string
    {
        // thumbnail directory path
        $thumbnailFilePathern = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/' . $token . '.jpg';

        // check if thumbnail found
        if (file_exists($thumbnailFilePathern)) {
            // get thumbnal file
            $thumbnal = file_get_contents($thumbnailFilePathern);

            // decrypt thumbnail
            $thumbnal = $this->securityUtil->decryptAES($thumbnal);

            // return thumbnail
            return $thumbnal;
        }

        return null;
    }

    /**
     * Preloads thumbnails for all media files in the system.
     *
     * Retrieves all media files from the repository and iterates through each one.
     * For each media file, it retrieves the associated media data and checks if it's an image or a video.
     * If it's a video, it fetches the thumbnail. Then, it checks if a thumbnail already exists for the media file.
     * If not, it stores the thumbnail.
     *
     * @param string $referer The referer indicating where the command originated from.
     * @param int $userId The user is for preload process.
     * @param string|null $galleryName The gallery name for preload process.
     *
     * @throws \Exception If an error occurs during the thumbnail preloading process.
     *
     * @return void
     */
    public function preloadAllThumbnails(string $referer = null, int $userId = null, string $galleryName = null): void
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
                if (str_contains($media['type'], 'video') && $_ENV['STORAGE_ENCRYPTION'] == 'true') {
                    continue;
                }

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
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to preload thumbnails: ' . $e->getMessage(), 500);
        }
    }
}
