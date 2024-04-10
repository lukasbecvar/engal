<?php

namespace App\Event\Subscriber;

use App\Event\ErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ErrorEventSubscriber
 *
 * Event subscriber for handling error events.
 * 
 * @package App\EventSubscriber
 */
class ErrorEventSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of subscribed events that this object should listen to.
     *
     * @return array<string> An array containing event names and corresponding methods to be called when events are dispatched.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ErrorEvent::NAME => 'onErrorEvent',
        ];
    }

    /**
     * Method called when an error event is dispatched.
     *
     * @param ErrorEvent $event The object representing the error event.
     * @return void
     */
    public function onErrorEvent(ErrorEvent $event): void
    {
        // get error values
        $error_name = $event->getErrorName();
        $error_message = $event->getErrorMessage();

        // log error here
    }
}
