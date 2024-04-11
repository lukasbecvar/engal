<?php

namespace App\Repository;

use App\Entity\BlacklistedToken;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<BlacklistedToken>
 *
 * @method BlacklistedToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlacklistedToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlacklistedToken[]    findAll()
 * @method BlacklistedToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlacklistedTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlacklistedToken::class);
    }
}
