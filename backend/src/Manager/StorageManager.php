<?php

namespace App\Manager;

use App\Entity\Media;
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
    private EntityManagerInterface $entityManager;

    public function __construct(ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
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
            // set entity data
            $media->setName($data['name']);
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
            $this->errorManager->handleError('error to store entity data: '.$e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return null;
        }
    }

    /**
     * Store media file.
     * 
     * @param string $token
     * @param object $file
     * @param int $user_id
     * @param string $file_type
     */
    public function storeMediaFile(string $token, object $file, int $user_id, string $file_type = 'photos'): void
    {
        // get uploaded file extension
        $file_extension = $file->getClientOriginalExtension();

        try {
            // check file type
            if (str_contains($file->getMimeType(), 'video')) {
                $file_type = 'videos';
            } 

            // move file to final storage directory
            $file->move(__DIR__.'/../../storage/'.$_ENV['APP_ENV'].'/'.$user_id.'/'.$file_type, $token.'.'.$file_extension);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to store media file: '.$e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
