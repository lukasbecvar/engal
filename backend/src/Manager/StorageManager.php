<?php

namespace App\Manager;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Intervention\Image\ImageManager;
use Symfony\Component\String\ByteString;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class StorageManager
 *
 * StorageManager class for manipulate with media storage filesystem & database
 *
 * @package App\Manager
 */
class StorageManager
{
    private ErrorManager $errorManager;
    private MediaRepository $mediaRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ErrorManager $errorManager, MediaRepository $mediaRepository, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Retrieves a media entity from the repository based on the provided search criteria.
     *
     * @param array<mixed> $search An associative array representing the search criteria.
     * @return object|null The found media entity or null if not found.
     */
    public function getMediaEntityRepository(array $search): ?object
    {
        return $this->entityManager->getRepository(Media::class)->findOneBy($search);
    }

    /**
     * Store media entity.
     *
     * @param array<string> $data
     * @return string|null
     */
    public function storeMediaEntity(array $data): ?string
    {
        // init media entity
        $media = new Media();

        // generate entity token
        $token = ByteString::fromRandom(32)->toString();

        // check if token not exist
        if ($this->getMediaEntityRepository(['token' => $token]) != null) {
            $this->storeMediaEntity($data);
        }

        try {
            // get media name
            $name = pathinfo($data['name'], PATHINFO_FILENAME);

            // set entity data
            $media->setName($name);
            $media->setGalleryName($data['gallery_name']);
            $media->setType($data['type']);
            $media->setOwnerId(intval($data['owner_id']));
            $media->setToken($token);
            $media->setUploadTime($data['upload_time']);
            $media->setLastEditTime('non-edited');

            // store data to database
            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $token;
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to store entity data: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return null;
        }
    }

    /**
     * Store media file.
     *
     * @param string $token
     * @param object $file
     * @param int $userId
     * @param string $fileType
     */
    public function storeMediaFile(string $token, object $file, int $userId, string $fileType = 'videos'): void
    {
        // get uploaded file extension
        $fileExtension = $file->getClientOriginalExtension();

        try {
            // check file type
            if (str_contains($file->getClientMimeType(), 'image')) {
                $fileType = 'photos';
            }

            // move file to final storage directory
            $file->move(__DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/' . $fileType, $token . '.' . $fileExtension);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to store media file: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieves the list of gallery names associated with a specific user ID.
     *
     * @param int $userId The ID of the user whose gallery names are to be retrieved.
     *
     * @return array<int<0,max>,array<string,string|null>> The array containing the gallery names.
     */
    public function getGalleryListByUserId(int $userId): array
    {
        $galleryNamesArray = [];

        // get gallery names
        $galleryNames = $this->mediaRepository->findDistinctGalleryNamesByUserId($userId);

        // build gallery list array
        foreach ($galleryNames as $name) {
            // get gallery name
            $name = $name['gallery_name'];

            $galleryNamesArray[] = [
                'name' => $name,
                'first_token' => $this->mediaRepository->findFirstTokenByGalleryName($name)
            ];
        }

        return $galleryNamesArray;
    }

    /**
     * Checks if media with the given token and owner ID exists.
     *
     * @param int $ownerId The ID of the owner of the media.
     * @param string $mediaToken The token of the media to check.
     * @return bool True if the media exists for the given owner, false otherwise.
     */
    public function isMediaExist(int $ownerId, string $mediaToken): bool
    {
        if ($this->getMediaEntityRepository(['token' => $mediaToken, 'owner_id' => $ownerId]) != null) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the media type associated with the provided media token.
     *
     * @param string $mediaToken The token associated with the media.
     *
     * @return string|null The media type if found, otherwise null.
     */
    public function getMediaType(string $mediaToken): ?string
    {
        return $this->getMediaEntityRepository(['token' => $mediaToken])->getType();
    }

    /**
     * Retrieves the path of the media file associated with the given user ID and token.
     *
     * @param int $userId The ID of the user.
     * @param string $token The token associated with the media file.
     *
     * @return string|null The path of the media file, or null if not found.
     */
    public function getMediaFile(int $userId, string $token)
    {
        // build media file path pathern
        $mediaPathPathern = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/*/' . $token . '.*';

        // get files in pathern
        $files = glob($mediaPathPathern);

        // check if media file found
        if ($files !== false && count($files) > 0) {
            return $files[0];
        } else {
            $this->errorManager->handleError('error to found media file: ' . $userId . ':' . $token, 404);
        }

        return null;
    }

    /**
     * Retrieves the content of the media file associated with the given user ID and token.
     *
     * @param int $userId The ID of the user.
     * @param string $token The token associated with the media file.
     *
     * @return string|null The content of the media file, or null if not found.
     */
    public function getMediaContent(int $userId, string $token): ?string
    {
        // get media file
        $file = $this->getMediaFile($userId, $token);

        // return file content
        return file_get_contents($file);
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
        $mediaType = $this->getMediaType($token);

        // select media to resize
        if (str_contains($mediaType, 'image')) {
            $mediaFile = $this->getMediaFile($userId, $token);
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
        // get video file
        $mediaFile = $this->getMediaFile($userId, $token);

        // build video thumnail file path
        $thumbnailFilename = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/' . $token . '.jpg';

        // create file path
        if (!file_exists($thumbnailFilename)) {
            // get video duration
            $duration = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $mediaFile");
            $duration = floatval($duration);

            // generate thumbnail (middle time)
            exec('ffmpeg -ss ' . ($duration / 2.1) . ' -i ' . $mediaFile . ' -vframes 1 ' . $thumbnailFilename);
        }

        return file_get_contents($thumbnailFilename);
    }

    /**
     * Store a thumbnail of the given media file and return the encoded thumbnail image.
     *
     * @param string $mediaFile The path to the original media file.
     * @param int $userId The user ID associated with the media.
     * @param string $token The unique token identifier for the thumbnail.
     * @return mixed The encoded thumbnail image object, or null if the thumbnail couldn't be created.
     */
    public function storeThumbnail(string $mediaFile, int $userId, string $token): mixed
    {
        // init Intervention manager
        $manager = new ImageManager(new Driver());

        // read media file
        $image = $manager->read($mediaFile);

        // get actual image property
        $width = $image->width();
        $height = $image->height();

        // check if image can by resized
        if ($width < 700 && $height < 1300) {
            return $this->getMediaContent($userId, $token);
        }

        // thumbnail directory path
        $thumbnailDirectory = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/';

        // create thumbnail directory
        if (!file_exists($thumbnailDirectory)) {
            mkdir($thumbnailDirectory, 0777, true);
        }

        // calculate new resolution
        $newWidth = $width * 0.3;
        $newHeight = $height * 0.3;

        // resize media file
        $image->resize((int) $newWidth, (int) $newHeight);

        // encode thumbnail image
        $image = $image->encode();

        // build thumbnail image pathern
        $thumbnailPathern = $thumbnailDirectory . $token . '.jpg';

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
     * @return string|null The content of the thumbnail image file, or null if the thumbnail doesn't exist.
     */
    public function getMediaExistThumbnail(int $userId, string $token): ?string
    {
        // thumbnail directory path
        $thumbnailFilePathern = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/thumbnails/' . $token . '.jpg';

        // check if thumbnail found
        if (file_exists($thumbnailFilePathern)) {
            // return thumbnal file
            return file_get_contents($thumbnailFilePathern);
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
     *                        If 'console_command', progress messages are printed to console outputs.
     *
     * @throws \Exception If an error occurs during the thumbnail preloading process.
     *
     * @return void
     */
    public function preloadAllThumbnails(string $referer = null): void
    {
        $mediaList = $this->mediaRepository->findAllMedia();

        try {
            foreach ($mediaList as $media) {
                // get media data
                $userId = $media['owner_id'];
                $token = $media['token'];

                // get media file
                $mediaFile = $this->getMediaFile($userId, $token);

                if (!str_contains($media['type'], 'image')) {
                    $mediaFile = $this->getVideoThumbnail($userId, $token);
                }

                // check if thumbnail is already exist
                if ($this->getMediaExistThumbnail($userId, $token) == null) {
                    $this->storeThumbnail($mediaFile, $userId, $token);
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
