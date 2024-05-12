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
     * Get media entity repository.
     *
     * @param string $token
     * @return object|null
     */
    public function getMediaEntityRepository(string $token): ?object
    {
        return $this->entityManager->getRepository(Media::class)->findOneBy(['token' => $token]);
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
        if ($this->getMediaEntityRepository($token) != null) {
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
}
