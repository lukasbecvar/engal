<?php

namespace App\Manager;

use Exception;
use Aws\S3\S3Client;
use App\Entity\Media;
use App\Util\SecurityUtil;
use Aws\Exception\AwsException;
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
    private ?string $sse;
    private string $bucket;
    private int $presignedTtl;
    private S3Client $s3Client;
    private S3Client $presignClient;
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private MediaRepository $mediaRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ErrorManager $errorManager,
        SecurityUtil $securityUtil,
        MediaRepository $mediaRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->mediaRepository = $mediaRepository;
        $this->securityUtil = $securityUtil;
        $this->bucket = $_ENV['S3_BUCKET'];
        $this->sse = $_ENV['S3_SSE'] ?? null;
        $this->presignedTtl = (int) ($_ENV['S3_PRESIGNED_TTL'] ?? 900);
        $clientConfig = [
            'version' => 'latest',
            'region' => $_ENV['S3_REGION'],
            'endpoint' => $_ENV['S3_ENDPOINT'],
            'use_path_style_endpoint' => ($_ENV['S3_USE_PATH_STYLE'] ?? 'false') === 'true',
            'credentials' => [
                'key' => $_ENV['S3_ACCESS_KEY'],
                'secret' => $_ENV['S3_SECRET_KEY'],
            ],
        ];
        $this->s3Client = new S3Client($clientConfig);

        // presign client may use a public endpoint reachable from browser
        if (!empty($_ENV['S3_PUBLIC_ENDPOINT'])) {
            $clientConfig['endpoint'] = $_ENV['S3_PUBLIC_ENDPOINT'];
        }
        $this->presignClient = new S3Client($clientConfig);

        $this->ensureBucketExists();
    }

    /**
     * Retrieves a media entity from the repository based on the provided search criteria
     *
     * @param array<mixed> $search An associative array representing the search criteria
     *
     * @return object|null The found media entity or null if not found
     */
    public function getMediaEntityRepository(array $search): ?object
    {
        return $this->entityManager->getRepository(Media::class)->findOneBy($search);
    }

    /**
     * Store media entity to database
     *
     * @param array<string> $data The data to store in the media entity
     *
     * @return string|null The token of the stored media entity, or null if an error occurred
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
            // get media data
            $name = pathinfo($data['name'], PATHINFO_FILENAME);
            $galleryName = $data['gallery_name'];

            // set entity data
            $media->setName($this->securityUtil->encryptName($name));
            $media->setGalleryName($this->securityUtil->encryptName($galleryName));
            $media->setType($data['type']);
            $media->setLength($data['length']);
            $media->setOwnerId(intval($data['owner_id']));
            $media->setToken($token);
            $media->setUploadTime($data['upload_time']);
            $media->setLastEditTime('non-edited');

            // store data to database
            $this->entityManager->persist($media);
            $this->entityManager->flush();

            return $token;
        } catch (Exception $e) {
            $this->errorManager->handleError('error to store entity data: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return null;
        }
    }

    /**
     * Store and media file
     *
     * @param string $token The unique token for the file
     * @param object $file The uploaded file object
     * @param int $userId The ID of the user uploading the file
     * @param string $fileType The type of the file (default: 'videos')
     *
     * @return void
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

            // read file content
            $fileContent = file_get_contents($file->getPathname());
            if ($fileContent === false) {
                $this->errorManager->handleError('failed to read file content', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            // encrypt the file content
            $encryptedContent = $fileContent;

            // upload to S3/MinIO
            $params = [
                'Bucket' => $this->bucket,
                'Key' => $this->buildObjectKey($userId, $fileType, $token, $fileExtension),
                'Body' => $encryptedContent,
                'ContentType' => $file->getClientMimeType(),
            ];

            if (!empty($this->sse)) {
                $params['ServerSideEncryption'] = $this->sse;
            }

            $this->s3Client->putObject($params);
        } catch (AwsException $e) {
            $this->errorManager->handleError('error to store media file to s3: ' . $e->getAwsErrorMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            $this->errorManager->handleError('error to store media file: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieves the list of gallery names associated with a specific user ID
     *
     * @param int $userId The ID of the user whose gallery names are to be retrieved
     *
     * @return array<int<0,max>,array<string,string|null>> The array containing the gallery names
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

            // decrypt gallery name
            $nameDec = $this->securityUtil->decryptName($name);

            // build gallery list array
            $galleryNamesArray[] = [
                'name' => $nameDec,
                'first_token' => $this->mediaRepository->findFirstTokenByProperty($userId, $nameDec)
            ];
        }

        return $galleryNamesArray;
    }

    /**
     * Checks if media with the given token and owner ID exists
     *
     * @param int $ownerId The ID of the owner of the media
     * @param string $mediaToken The token of the media to check
     * @param bool $canCrash The specify if this process can crash without valid response
     *
     * @return bool True if the media exists for the given owner, false otherwise
     */
    public function isMediaExist(int $ownerId, string $mediaToken, bool $canCrash = true): bool
    {
        // check if entity exist in database
        if ($this->getMediaEntityRepository(['token' => $mediaToken]) != null) {
            // check if media file exist
            if ($this->findMediaObjectKey($ownerId, $mediaToken) !== null) {
                return true;
            } else {
                // check if process can crash before complete
                if ($canCrash) {
                    $this->errorManager->handleError('error to get media: ' . $mediaToken . ' but entity exist', 404);
                }
                return false;
            }
        }

        return false;
    }

    /**
     * Retrieves the media type associated with the provided media token
     *
     * @param string $mediaToken The token associated with the media
     *
     * @return string|null The media type if found, otherwise null
     */
    public function getMediaType(string $mediaToken): ?string
    {
        return $this->getMediaEntityRepository(['token' => $mediaToken])->getType();
    }

    /**
     * Get media file metadata (folder, key, extension)
     *
     * @return array<string,string>|null
     */
    public function getMediaFileMeta(int $userId, string $token): ?array
    {
        $mediaObject = $this->findMediaObjectKey($userId, $token);

        if ($mediaObject === null) {
            return null;
        }

        return [
            'key' => $mediaObject['key'],
            'folder' => $mediaObject['folder'],
            'extension' => pathinfo($mediaObject['key'], PATHINFO_EXTENSION),
        ];
    }

    /**
     * Retrieves the path of the media file associated with the given user ID and token
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media file
     * @param bool $canCrash The specify if this process can crash without valid response
     *
     * @return string|null The path of the media file, or null if not found
     */
    public function getMediaFile(int $userId, string $token, bool $canCrash = true): ?string
    {
        $mediaObject = $this->findMediaObjectKey($userId, $token);

        if ($mediaObject === null) {
            if ($canCrash) {
                $this->errorManager->handleError('error to found media file: ' . $userId . ':' . $token, 404);
            }

            return null;
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'engal_media_');

        try {
            $response = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $mediaObject['key'],
            ]);

            file_put_contents($tempFile, (string) $response['Body']);

            return $tempFile;
        } catch (AwsException $e) {
            if ($canCrash) {
                $this->errorManager->handleError('error to download media file: ' . $e->getAwsErrorMessage(), 404);
            }
        } catch (Exception $e) {
            if ($canCrash) {
                $this->errorManager->handleError('error to download media file: ' . $e->getMessage(), 404);
            }
        }

        return null;
    }

    /**
     * Retrieves the content of the media file associated with the given user ID and token
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media file
     *
     * @return string|null The content of the media file, or null if not found
     */
    public function getMediaContent(int $userId, string $token): ?string
    {
        // get media file
        $file = $this->getMediaFile($userId, $token);

        // get file content
        $content = file_get_contents($file);

        // decrypt token
        $content = $content;

        // return content
        return $content;
    }

    /**
     * Retrieve information about all media files
     *
     * This method retrieves information about all media files stored in the system
     *
     * @return array<int<0,max>,array<string,string>> An array containing information about each media file. Each element of the array is an associative array with the following keys:
     *   - 'folder' (string): The folder where the media file is stored
     *   - 'user_id' (string): The ID of the user associated with the media file
     *   - 'token' (string): The token identifying the media file, without the file extension
     */
    public function getAllMediaFiles(): array
    {
        try {
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $this->bucket,
                'Prefix' => $_ENV['APP_ENV'] . '/',
            ]);

            if (!isset($objects['Contents'])) {
                return [];
            }

            $result = [];

            foreach ($objects['Contents'] as $object) {
                $parts = explode('/', $object['Key']);

                if (count($parts) !== 4) {
                    continue;
                }

                [$env, $userId, $folder, $filename] = $parts;

                if ($folder === 'thumbnails') {
                    continue;
                }

                $result[] = [
                    'folder' => $folder,
                    'user_id' => $userId,
                    'token' => pathinfo($filename, PATHINFO_FILENAME),
                ];
            }

            return $result;
        } catch (AwsException $e) {
            $this->errorManager->handleError('error to list media files: ' . $e->getAwsErrorMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return [];
        }
    }

    /**
     * Store thumbnail content to object storage
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media file
     * @param string $content The content to store
     *
     * @return void
     */
    public function storeThumbnailContent(int $userId, string $token, string $content): void
    {
        try {
            $encrypted = $content;

            $params = [
                'Bucket' => $this->bucket,
                'Key' => $this->buildObjectKey($userId, 'thumbnails', $token, 'jpg'),
                'Body' => $encrypted,
                'ContentType' => 'image/jpeg',
            ];

            if (!empty($this->sse)) {
                $params['ServerSideEncryption'] = $this->sse;
            }

            $this->s3Client->putObject($params);
        } catch (AwsException $e) {
            $this->errorManager->handleError('error to store thumbnail to s3: ' . $e->getAwsErrorMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get thumbnail content from object storage.
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media file
     *
     * @return string|null The content of the thumbnail, or null if not found
     */
    public function getThumbnailContent(int $userId, string $token): ?string
    {
        $thumbnailObject = $this->findMediaObjectKey($userId, $token, ['thumbnails']);

        if ($thumbnailObject === null) {
            return null;
        }

        try {
            $response = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $thumbnailObject['key'],
            ]);

            return (string) $response['Body'];
        } catch (AwsException) {
            return null;
        }
    }

    /**
     * Generate a presigned URL for a media object.
     *
     * @param int $userId The ID of the user
     * @param string $token The token associated with the media file
     * @param int|null $expires The expiration time in seconds for the presigned URL, defaults to the configured presigned TTL
     *
     * @return string|null The presigned URL if found, otherwise null
     */
    public function generatePresignedUrl(int $userId, string $token, ?int $expires = null): ?string
    {
        $object = $this->findMediaObjectKey($userId, $token);

        if ($object === null) {
            return null;
        }

        $ttl = $this->normalizePresignedTtl($expires ?? $this->presignedTtl);

        $cmd = $this->presignClient->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $object['key'],
        ]);

        $request = $this->presignClient->createPresignedRequest($cmd, '+' . $ttl . ' seconds');

        return (string) $request->getUri();
    }

    /**
     * Default presigned TTL (seconds)
     *
     * @return int The default presigned TTL in seconds
     */
    public function getDefaultPresignedTtl(): int
    {
        return $this->presignedTtl;
    }

    /**
     * Store arbitrary media content (fixtures/cli) to object storage
     *
     * @param int $userId The ID of the user
     * @param string $folder The folder where the media file is stored
     * @param string $token The token associated with the media file
     * @param string $extension The file extension
     * @param string $mimeType The MIME type of the file
     * @param string $content The content to store
     *
     * @return void
     */
    public function storeRawContent(int $userId, string $folder, string $token, string $extension, string $mimeType, string $content): void
    {
        try {
            $encrypted = $content;

            $params = [
                'Bucket' => $this->bucket,
                'Key' => $this->buildObjectKey($userId, $folder, $token, $extension),
                'Body' => $encrypted,
                'ContentType' => $mimeType,
            ];

            if (!empty($this->sse)) {
                $params['ServerSideEncryption'] = $this->sse;
            }

            $this->s3Client->putObject($params);
        } catch (AwsException $e) {
            $this->errorManager->handleError('error to store raw media content: ' . $e->getAwsErrorMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Build storage object key path
     *
     * @param int $userId The ID of the user
     * @param string $folder The folder where the media file is stored
     * @param string $token The token associated with the media file
     * @param string|null $extension The file extension
     *
     * @return string The object key path
     */
    private function buildObjectKey(int $userId, string $folder, string $token, ?string $extension = null): string
    {
        $ext = $extension !== null ? '.' . ltrim($extension, '.') : '';

        return sprintf('%s/%d/%s/%s%s', $_ENV['APP_ENV'], $userId, $folder, $token, $ext);
    }

    /**
     * Find first matching object key for the given token
     *
     * @param array<int, string> $folders The folders to search in
     *
     * @return array{key: string, folder: string}|null
     */
    private function findMediaObjectKey(int $userId, string $token, array $folders = ['photos', 'videos']): ?array
    {
        foreach ($folders as $folder) {
            $prefix = $this->buildObjectKey($userId, $folder, $token);

            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $this->bucket,
                'Prefix' => $prefix,
                'MaxKeys' => 1,
            ]);

            if (!empty($objects['Contents'][0]['Key'])) {
                return [
                    'key' => $objects['Contents'][0]['Key'],
                    'folder' => $folder,
                ];
            }
        }

        return null;
    }

    /**
     * Clamp presigned TTL to a safe window
     *
     * @param int $ttl The presigned TTL
     *
     * @return int The clamped presigned TTL
     */
    private function normalizePresignedTtl(int $ttl): int
    {
        // clamp to exactly 5 minutes to avoid long-lived public links
        return max(300, min($ttl, 300));
    }

    /**
     * Ensure bucket exists (create if missing)
     *
     * @return void
     */
    private function ensureBucketExists(): void
    {
        try {
            $this->s3Client->headBucket(['Bucket' => $this->bucket]);
        } catch (AwsException $e) {
            if ($e->getStatusCode() !== 404) {
                return;
            }

            $params = ['Bucket' => $this->bucket];

            if ($_ENV['S3_REGION'] !== 'us-east-1') {
                $params['CreateBucketConfiguration'] = [
                    'LocationConstraint' => $_ENV['S3_REGION'],
                ];
            }

            $this->s3Client->createBucket($params);
            $this->s3Client->waitUntil('BucketExists', ['Bucket' => $this->bucket]);
        }
    }
}
