<?php

namespace App\Event\Subscriber;

use App\Manager\LogManager;
use App\Manager\UserManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

/**
 * Class LoginEventSubscriber
 *
 * Subscriber to handle events related to user login authentication success.
 *
 * @package App\EventSubscriber
 */
class LoginEventSubscriber implements EventSubscriberInterface
{
    private LogManager $logManager;
    private UserManager $userManager;
    private RequestStack $requestStack;

    public function __construct(LogManager $logManager, UserManager $userManager, RequestStack $requestStack)
    {
        $this->logManager = $logManager;
        $this->userManager = $userManager;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
        ];
    }

    /**
     * Handles the authentication success event.
     *
     * @param AuthenticationSuccessEvent $event The authentication success event.
     */
    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $pathInfo = $request->getPathInfo();

        // check if request is login
        if ($pathInfo == '/api/login') {
            // get username
            $identifier = $event->getAuthenticationToken()->getUser()->getUserIdentifier();

            // update user data
            $this->userManager->updateUserDataOnLogin($identifier);

            // log user auth
            $this->logManager->log('authenticator', 'user: ' . $identifier . ' loggedin');
        }
    }
}
