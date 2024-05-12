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
}
