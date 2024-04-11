<?php

namespace App\Manager;

use Psr\Cache\CacheItemPoolInterface;
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
    private CacheItemPoolInterface $cache;

    /**
     * AuthTokenManager constructor.
     *
     * @param CacheItemPoolInterface $cache The cache item pool
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Checks if a token is blacklisted.
     *
     * @param string $token The token to check
     * 
     * @return bool True if the token is blacklisted, false otherwise
     */
    public function isTokenBlacklisted(string $token): bool
    {
        return $this->cache->hasItem($token);
    }

    /**
     * Blacklists a token.
     *
     * @param string $token The token to blacklist
     */
    public function blacklistToken(string $token): void
    {
        $item = $this->cache->getItem($token);
        $item->set(null);
        $this->cache->save($item);
    }

    /**
     * Removes a token from the blacklist.
     *
     * @param string $token The token to unblacklist
     */
    public function unblacklistToken(string $token): void
    {
        $this->cache->deleteItem($token);
    }

    /**
     * Retrieves the token from the request headers.
     *
     * @param Request $request The request object
     * 
     * @return string|null The token if found, otherwise null
     */
    public function getTokenFromRequest(Request $request): ?string
    {
        return $request->headers->get('Authorization');
    }
}
