<?php

namespace App\Middleware;

use App\Manager\ErrorManager;
use App\Manager\AuthTokenManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class AuthTokenValidateMiddleware
 * 
 * Middleware for validating authentication tokens.
 * 
 * @package App\Middleware
 */
class AuthTokenValidateMiddleware
{
    private ErrorManager $errorManager;
    private AuthTokenManager $authTokenManager;

    /**
     * AuthTokenValidateMiddleware constructor.
     *
     * @param ErrorManager $errorManager The error manager
     * @param AuthTokenManager $authTokenManager The authentication token manager
     */
    public function __construct(ErrorManager $errorManager, AuthTokenManager $authTokenManager)
    {
        $this->errorManager = $errorManager;
        $this->authTokenManager = $authTokenManager;
    }

    /**
     * Handles the kernel request event.
     *
     * @param RequestEvent $event The request event
     * 
     * @throws \Exception If there is an error while processing the request
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $token = $this->authTokenManager->getTokenFromRequest($request);

        // check if token is not blacklisted
        if (!empty($token)) {
            if ($this->authTokenManager->isTokenBlacklisted($token)) {
                $this->errorManager->handleError('Invalid JWT token', 401, false);
            }
        }
    }
}
