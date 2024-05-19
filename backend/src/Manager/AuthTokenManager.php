<?php

namespace App\Manager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

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
    private CacheManager $cacheManager;
    private JWTEncoderInterface $jwtEncoderInterface;

    public function __construct(ErrorManager $errorManager, CacheManager $cacheManager, JWTEncoderInterface $jwtEncoderInterface)
    {
        $this->errorManager = $errorManager;
        $this->cacheManager = $cacheManager;
        $this->jwtEncoderInterface = $jwtEncoderInterface;
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
            return $this->cacheManager->isCatched('auth_token_' . $token);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to check if token is blacklisted: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return false;
        }
    }

    /**
     * Blacklists a token.
     *
     * @param string $token The token to blacklist
     *
     * @throws \Exception If there is an error while blacklisting the token
     *
     * @return void
     */
    public function blacklistToken(string $token): void
    {
        try {
            if (!$this->isTokenBlacklisted($token)) {
                $this->cacheManager->setValue('auth_token_' . $token, 'auth_token', 604800);
            }
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to blacklisted token: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Removes a token from the blacklist.
     *
     * @param string $token The token to unblacklist
     *
     * @throws \Exception If there is an error while unblacklisting the token
     *
     * @return void
     */
    public function unblacklistToken(string $token): void
    {
        try {
            $this->cacheManager->deleteValue('auth_token_' . $token);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to unblacklisted token: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
            $this->errorManager->handleError('error to get auth token: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return null;
        }
    }

    /**
     * Decodes a JWT token.
     *
     * @param string $token The JWT token to decode.
     *
     * @throws \Exception If there is an error while decoding the token.
     *
     * @return array<mixed> The decoded token data.
     */
    public function decodeToken(string $token): array
    {
        try {
            return $this->jwtEncoderInterface->decode($token);
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to decode token: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return [];
        }
    }

    /**
     * Checks if a JWT token is valid.
     *
     * @param string $token The JWT token to validate.
     *
     * @throws \Exception If there is an error while validating the token.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function isTokenValid(string $token): bool
    {
        try {
            // decode the token
            $decodedToken = $this->decodeToken($token);

            // check if the token is expired
            if ($decodedToken['exp'] < time()) {
                return false;
            }

            // check if token is blacklisted
            if ($this->isTokenBlacklisted($token)) {
                return false;
            }

            // the token is valid
            return true;
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to validate token: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return false;
        }
    }
}
