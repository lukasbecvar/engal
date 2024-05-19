<?php

namespace App\Event\Subscriber;

use App\Manager\LogManager;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExceptionEventSubscriber
 *
 * Subscriber to handle internal (profiler) errors.
 *
 * @package App\EventSubscriber
 */
class ExceptionEventSubscriber implements EventSubscriberInterface
{
    private LogManager $logManager;

    public function __construct(LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array<string> The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * Method called when the KernelEvents::EXCEPTION event is dispatched.
     *
     * @param ExceptionEvent $event The event object
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        // get exception data
        $exception = $event->getThrowable();

        // log exception
        $this->logManager->log('exception', $exception->getMessage());

        // init response type
        $response = new JsonResponse();

        // set response data
        if ($_ENV['APP_ENV'] == 'prod') {
            $responseData = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Unknown server error',
            ];
        } else {
            $responseData = [
                'status' => 'error',
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];
        }

        // set response data
        $response->setData($responseData);

        // send response
        $event->setResponse($response);
    }
}
