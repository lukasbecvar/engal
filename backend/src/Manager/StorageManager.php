<?php

namespace App\Manager;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\ByteString;

/**
 * Class StorageManager
 * 
 * StorageManager class for manipulate with media storage filesystem & database
 * 
 * @package App\Manager
 */
class StorageManager
{
    private UserManager $userManager;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(UserManager $userManager, ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->userManager = $userManager;
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
     * Create storage directory.
     * 
     * @param int $id
     * @param string $type
     */
    public function createStorageDir(int $id, string $type): void
    {
        $path = __DIR__.'/../../storage/'.$_ENV['APP_ENV'].'/'.$id.'/'.$type;
        
        if (!file_exists($path)) {
            try {
                mkdir($path, 777, true);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to create storage directory: '.$e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
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
     * @param object $security
     * @param string $file_type
     */
    public function storeMediaFile(string $token, object $file, object $security, string $file_type = 'photos'): void
    {
        // get uploaded file extension
        $file_extension = $file->getClientOriginalExtension();

        // get uploader user ID
        $user_id = $this->userManager->getUserData($security)->getID();

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
