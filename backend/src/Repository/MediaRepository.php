<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    /**
     * Retrieves all media files from the repository.
     *
     * This method fetches all media files from the database and returns an array of media data,
     * including the media ID, owner ID, type, and token.
     *
     * @return array<array<int|string>> The array containing media data, each element representing a media file.
     */
    public function findAllMedia(): array
    {
        return $this->createQueryBuilder('m')->select('m.id, m.owner_id, m.type, m.token')->getQuery()->getResult();
    }

    /**
     * Finds distinct gallery names for a given user ID.
     *
     * @param int $userId The ID of the user
     * @return array<array<string>> The array of distinct gallery names
     */
    public function findDistinctGalleryNamesByUserId(int $userId): array
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.gallery_name')
            ->where('m.owner_id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Counts the number of media records based on owner ID and type.
     *
     * @param int         $ownerId The ID of the owner.
     * @param string|null $type    (Optional) The type of media. If null, counts all media containing 'image' in type.
     *
     * @return int The number of media records.
     */
    public function countMediaByType(int $ownerId, string $type = null): int
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
     * Finds the first token by gallery name.
     *
     * @param int $ownerId The account id of gallery owner.
     * @param string $galleryName The name of the gallery.
     *
     * @return string|null The token or null if not found.
     */
    public function findFirstTokenByGalleryName(int $ownerId, string $galleryName): ?string
    {
        $result = $this->createQueryBuilder('m')
            ->select('m.token')
            ->andWhere('m.gallery_name = :gallery_name')
            ->andWhere('m.owner_id = :owner_id')
            ->setParameter('gallery_name', $galleryName)
            ->setParameter('owner_id', $ownerId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['token'] ?? null;
    }

    /**
     * Checks if a gallery exists for a given owner ID and gallery name.
     *
     * @param int    $ownerId     The ID of the owner.
     * @param string $galleryName The name of the gallery.
     * @return bool True if the gallery exists, false otherwise.
     */
    public function isGalleryExists(int $ownerId, string $galleryName): bool
    {
        $result = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.owner_id = :owner_id')
            ->andWhere('m.gallery_name = :gallery_name')
            ->setParameter('owner_id', $ownerId)
            ->setParameter('gallery_name', $galleryName)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * Finds all media associated with a given gallery name and owner ID.
     *
     * @param int    $ownerId     The ID of the owner.
     * @param string $galleryName The name of the gallery.
     * @return array<mixed> The array of media entities.
     */
    public function findAllByGalleryName(int $ownerId, string $galleryName): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.gallery_name = :gallery_name')
            ->setParameter('gallery_name', $galleryName)
            ->andWhere('m.owner_id = :owner_id')
            ->setParameter('owner_id', $ownerId);

        $result = $qb->getQuery()->getResult();

        // defalut media places
        $images = [];
        $videos = [];

        // split result types
        foreach ($result as $media) {
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
}
