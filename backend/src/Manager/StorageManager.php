<?php

namespace App\Manager;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\String\ByteString;
use Doctrine\ORM\EntityManagerInterface;
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
     * @return array<string> The array containing the gallery names.
     */
    public function getGalleryListByUserId(int $userId): array
    {
        $galleryNamesArray = [];

        // get gallery names
        $galleryNames = $this->mediaRepository->findDistinctGalleryNamesByUserId($userId);

        // build gallery list array
        foreach ($galleryNames as $name) {
            $galleryNamesArray[] = $name['gallery_name'];
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
     * Retrieves the content of the media associated with the provided user ID and token.
     *
     * @param int $userId The ID of the user associated with the media.
     * @param string $token The token associated with the media.
     *
     * @return string|null The content of the media if found, otherwise null.
     */
    public function getMediaContent(int $userId, string $token): ?string
    {
        $mediaPathPathern = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/' . $userId . '/*/' . $token . '.*';

        // get files by mediaPath pathern
        $files = glob($mediaPathPathern);

        // check if file found
        if ($files !== false && count($files) > 0) {
            // select file
            $firstFile = $files[0];
            return file_get_contents($firstFile);
        }

        return null;
    }
}
