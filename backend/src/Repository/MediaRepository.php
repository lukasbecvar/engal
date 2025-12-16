<?php

namespace App\Repository;

use App\Entity\Media;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class MediaRepository
 *
 * Repository for media database entity
 *
 * @extends ServiceEntityRepository<Media>
 *
 * @package App\Repository
 */
class MediaRepository extends ServiceEntityRepository
{
    private SecurityUtil $securityUtil;
    private bool $encryptionEnabled;

    public function __construct(ManagerRegistry $registry, SecurityUtil $securityUtil)
    {
        parent::__construct($registry, Media::class);
        $this->securityUtil = $securityUtil;
        $this->encryptionEnabled = ($_ENV['STORAGE_ENCRYPTION_ENABLED'] ?? 'false') === 'true';
    }

    /**
     * Find all media items by owner ID
     *
     * @param int $ownerId The ID of the owner whose media items to retrieve
     *
     * @return array<array<int|string>> An array containing all media items belonging to the specified owner
     */
    public function findAllMediaByOwnerId(int $ownerId): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.id, m.owner_id, m.type, m.token')
            ->where('m.owner_id = :owner_id')
            ->setParameter('owner_id', $ownerId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all media items by gallery name for a given owner
     *
     * @param int    $ownerId     The ID of the owner
     * @param string $galleryName The name of the gallery
     *
     * @return array<array<int|string>> An array containing the found media items
     */
    public function findAllMediaByGalleryName(int $ownerId, string $galleryName): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.id, m.owner_id, m.type, m.token')
            ->where('m.owner_id = :owner_id AND m.gallery_name IN (:gallery_names)')
            ->setParameter('owner_id', $ownerId)
            ->setParameter('gallery_names', $this->buildGalleryNames($galleryName))
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieves all media files from the repository
     *
     * This method fetches all media files from the database and returns an array of media data,
     * including the media ID, owner ID, type, and token
     *
     * @return array<array<int|string>> The array containing media data, each element representing a media file
     */
    public function findAllMedia(): array
    {
        return $this->createQueryBuilder('m')->select('m.id, m.owner_id, m.type, m.token')->getQuery()->getResult();
    }

    /**
     * Finds distinct gallery names for a given user ID
     *
     * @param int $userId The ID of the user
     *
     * @return array<array<string>> The array of distinct gallery names
     */
    public function findDistinctGalleryNamesByUserId(int $userId): array
    {
        $raw = $this->createQueryBuilder('m')
            ->select('DISTINCT m.gallery_name')
            ->where('m.owner_id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();

        $result = [];

        foreach ($raw as $row) {
            $decrypted = $this->securityUtil->decryptName($row['gallery_name']);
            if (!in_array($decrypted, $result, true)) {
                $result[] = $decrypted;
            }
        }

        return array_map(static fn(string $name) => ['gallery_name' => $name], $result);
    }

    /**
     * Counts the number of media records based on owner ID and type
     *
     * @param int         $ownerId The ID of the owner
     * @param string|null $type    (Optional) The type of media. If null, counts all media containing 'image' in type
     *
     * @return int The number of media records
     */
    public function countMediaByType(int $ownerId, ?string $type = null): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.owner_id = :owner_id')
            ->setParameter('owner_id', $ownerId);

        // select where parameter
        if ($type == null) {
            $qb->andWhere($qb->expr()->like('m.type', ':type'))->setParameter('type', '%' . $type . '%');
        } else {
            // select image types
            $qb->andWhere($qb->expr()->notLike('m.type', ':type'))->setParameter('type', '%image%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Finds the first token by gallery name
     *
     * @param int $ownerId The account id of gallery owner
     * @param string $galleryName The name of the gallery
     *
     * @return string|null The token or null if not found
     */
    public function findFirstTokenByProperty(int $ownerId, string $galleryName): ?string
    {
        $result = $this->createQueryBuilder('m')
            ->select('m.token')
            ->andWhere('m.gallery_name IN (:gallery_names)')
            ->andWhere('m.owner_id = :owner_id')
            ->setParameter('gallery_names', $this->buildGalleryNames($galleryName))
            ->setParameter('owner_id', $ownerId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['token'] ?? null;
    }

    /**
     * Checks if a gallery exists for a given owner ID and gallery name
     *
     * @param int    $ownerId     The ID of the owner
     * @param string $galleryName The name of the gallery
     *
     * @return bool True if the gallery exists, false otherwise
     */
    public function isGalleryExists(int $ownerId, string $galleryName): bool
    {
        $result = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.owner_id = :owner_id')
            ->andWhere('m.gallery_name IN (:gallery_names)')
            ->setParameter('owner_id', $ownerId)
            ->setParameter('gallery_names', $this->buildGalleryNames($galleryName))
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * Finds all media associated with a given gallery name and owner ID
     *
     * @param int    $ownerId     The ID of the owner
     * @param string $galleryName The name of the gallery
     *
     * @return array<mixed> The array of media entities
     */
    public function findAllByProperty(int $ownerId, string $galleryName): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.gallery_name IN (:gallery_names)')
            ->setParameter('gallery_names', $this->buildGalleryNames($galleryName))
            ->andWhere('m.owner_id = :owner_id')
            ->setParameter('owner_id', $ownerId);

        $result = $qb->getQuery()->getResult();

        // defalut media places
        $images = [];
        $videos = [];

        // split result types
        foreach ($result as $media) {
            // get encrypted name
            $name = $media->getName();

            // decrypt name
            $name = $this->securityUtil->decryptName((string) $name);

            // set decrypted name
            $media->setName($name);

            if (str_contains($media->getType(), 'image')) {
                $images[] = $media;
            } else {
                $videos[] = $media;
            }
        }

        // merge results
        $mergedResult = array_merge($images, $videos);

        // return final content list
        return $mergedResult;
    }

    /**
     * Build possible stored variants (encrypted + legacy plaintext) for queries
     *
     * @return array<int, string> The array of possible stored variants
     */
    private function buildGalleryNames(string $galleryName): array
    {
        $encrypted = $this->securityUtil->encryptName($galleryName);

        if ($this->encryptionEnabled && $encrypted !== $galleryName) {
            return [$encrypted, $galleryName];
        }

        return [$encrypted];
    }
}
