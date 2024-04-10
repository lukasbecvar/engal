<?php

namespace App\Manager;

use App\Event\ErrorEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ErrorManager
 * 
 * ErrorManager handles error messages and their dispatching.
 * 
 * @package App\Manager
 */
class ErrorManager
{
    private EventDispatcherInterface $eventDispatcherInterface;

    /**
     * ErrorManager constructor.
     * @param EventDispatcherInterface $eventDispatcherInterface
     */
    public function __construct(EventDispatcherInterface $eventDispatcherInterface)
    {
        $this->eventDispatcherInterface = $eventDispatcherInterface;
    }

    /**
     * Handles errors by generating a JSON response and potentially dispatching an error event.
     *
     * @param string $message The error message.
     * @param int $code The error code.
     * @return void
     */
    public function handleError(string $message, int $code): void
    {
        // dispatch error event
        if ($this->canBeEventDispatched($message)) {
            $this->eventDispatcherInterface->dispatch(new ErrorEvent($code, 'internal-error', $message), ErrorEvent::NAME);
        }

        // protect error message in production env
        if ($_ENV['APP_ENV'] == 'prod') {
            $code = 500;
            $message = 'Unexpected server error';
        }

        // build error response
        $response = new JsonResponse([
            'error' => [
                'status' => 'error',
                'code' => $code,
                'message' => $message
            ]
        ], $code);

        // send error response
        $response->send();

        // force die app
        die();
    }

    /**
     * Checks if an error message can be dispatched as an event.
     *
     * @param string $error_message The error message to check.
     * @return bool True if the error can be dispatched as an event, false otherwise.
     */
    public function canBeEventDispatched(string $error_message): bool
    {
        // list of error patterns that should block event dispatch
        $blocked_error_patterns = [
            'log-error:', 
            'Unknown database',
            'Base table or view not found'
        ];
        
        // loop through each blocked error pattern
        foreach ($blocked_error_patterns as $pattern) {
            // check if the current pattern exists in the error message
            if (strpos($error_message, $pattern) !== false) {
                // if a blocked pattern is found, return false
                return false;
            }
        }
        
        // if no blocked patterns are found, return true
        return true;
    }
}
