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
     * @param string $galleryName The name of the gallery.
     * @return string|null The token or null if not found.
     */
    public function findFirstTokenByGalleryName(string $galleryName): ?string
    {
        $result = $this->createQueryBuilder('m')
            ->select('m.token')
            ->andWhere('m.gallery_name = :gallery_name')
            ->setParameter('gallery_name', $galleryName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['token'] ?? null;
    }
}
