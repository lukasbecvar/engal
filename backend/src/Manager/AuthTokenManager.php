<?php

namespace App\Manager;

use App\Entity\BlacklistedToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthTokenManager
 *
 * Manages authentication tokens, including blacklisting and retrieval from request.
 * 
 * @package App\Manager
 */
class AuthTokenManager
{
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    /**
     * AuthTokenManager constructor.
     *
     * @param ErrorManager $errorManager The error manager
     * @param EntityManagerInterface $entityManager The entity manager
     */
    public function __construct(ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Checks if a token is blacklisted.
     *
     * @param string $token The token to check
     * 
     * @return bool True if the token is blacklisted, false otherwise
     * 
     * @throws \Exception If there is an error while checking if the token is blacklisted
     */
    public function isTokenBlacklisted(string $token): bool
    {
        try {
            $blacklistedToken = $this->entityManager->getRepository(BlacklistedToken::class)->findOneBy(['token' => $token]);
            return $blacklistedToken !== null;
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to check if token is blacklisted: '.$e->getMessage(), 500);
            return false;
        }
    }

    /**
     * Blacklists a token.
     *
     * @param string $token The token to blacklist
     * 
     * @throws \Exception If there is an error while blacklisting the token
     */
    public function blacklistToken(string $token): void
    {
        try {
            if (!$this->isTokenBlacklisted($token)) {
                $blacklistedToken = new BlacklistedToken();
                $blacklistedToken->setToken($token);
                $blacklistedToken->setTime(date('d.m.Y H:i:s'));
                $this->entityManager->persist($blacklistedToken);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to blacklisted token: '.$e->getMessage(), 500);
        }
    }

    /**
     * Removes a token from the blacklist.
     *
     * @param string $token The token to unblacklist
     * 
     * @throws \Exception If there is an error while unblacklisting the token
     */
    public function unblacklistToken(string $token): void
    {
        try {
            $blacklistedToken = $this->entityManager->getRepository(BlacklistedToken::class)->findOneBy(['token' => $token]);
            if ($blacklistedToken !== null) {
                $this->entityManager->remove($blacklistedToken);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to unblacklisted token: '.$e->getMessage(), 500);
        }
    }

    /**
     * Deletes all tokens from the database and resets ID sequence.
     *
     * @throws \Exception If there is an error while deleting tokens
     */
    public function truncateBlacklistedTokens(): void
    {
        try {
            // create a query builder for the BlacklistedToken entity
            $queryBuilder = $this->entityManager->createQueryBuilder();

            // create a delete query for the BlacklistedToken entity
            $queryBuilder->delete(BlacklistedToken::class, 't')->getQuery()->execute();

            // reset ID sequence
            $connection = $this->entityManager->getConnection();
            $platform = $connection->getDatabasePlatform();
            $connection->executeStatement($platform->getTruncateTableSQL($this->entityManager->getClassMetadata(BlacklistedToken::class)->getTableName(), true));
        } catch (\Exception $e) {
            // handle the exception
            $this->errorManager->handleError('error to deleting tokens: '.$e->getMessage(), 500);

        }
    }

    /**
     * Retrieves the token from the request headers.
     *
     * @param Request $request The request object
     * 
     * @return string|null The token if found, otherwise null
     * 
     * @throws \Exception If there is an error while getting the auth token from the request
     */
    public function getTokenFromRequest(Request $request): ?string
    {
        try {
            return $request->headers->get('Authorization');
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get auth token: '.$e->getMessage(), 500);
            return null;
        }
    }
}
