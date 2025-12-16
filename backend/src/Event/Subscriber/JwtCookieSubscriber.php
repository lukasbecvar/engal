<?php

namespace App\Event\Subscriber;

use Symfony\Component\HttpFoundation\Cookie;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * Class JwtCookieSubscriber
 *
 * Subscriber to handle JWT cookie
 *
 * @package App\EventSubscriber
 */
class JwtCookieSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to
     *
     * @return array<string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    /**
     * Method called when the AuthenticationSuccessEvent event is dispatched
     *
     * @param AuthenticationSuccessEvent $event The event object
     *
     * @return void
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        if (!isset($data['token'])) {
            return;
        }

        $token = $data['token'];
        $response = $event->getResponse();

        $secure = ($_ENV['SSL_ONLY'] ?? 'false') === 'true';
        $cookie = Cookie::create('auth_token')
            ->withValue($token)
            ->withHttpOnly(true)
            ->withSecure($secure)
            ->withSameSite('lax')
            ->withPath('/');

        $response->headers->setCookie($cookie);

        // do not expose token in body; return simple status payload
        $event->setData([
            'status' => 'success'
        ]);
    }
}
