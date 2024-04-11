<?php

namespace App\Event\Subscriber;

use App\Manager\LogManager;
use App\Manager\UserManager;
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

    /**
     * LoginEventSubscriber constructor.
     *
     * @param LogManager $logManager
     * @param UserManager $userManager
     */
    public function __construct(LogManager $logManager, UserManager $userManager)
    {
        $this->logManager = $logManager;
        $this->userManager = $userManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<string,string> The event names to listen to
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
        $identifier = $event->getAuthenticationToken()->getUser()->getUserIdentifier();

        // update user data
        $this->userManager->updateUserDataOnLogin($identifier);

        // log user auth
        $this->logManager->log('authenticator', 'user: '.$identifier.' loggedin');
    }
}
