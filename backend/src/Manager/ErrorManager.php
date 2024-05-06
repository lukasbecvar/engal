<?php

namespace App\Manager;

use App\Util\SiteUtil;
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
    private SiteUtil $siteUtil;
    private EventDispatcherInterface $eventDispatcherInterface;

    public function __construct(SiteUtil $siteUtil, EventDispatcherInterface $eventDispatcherInterface)
    {
        $this->siteUtil = $siteUtil;
        $this->eventDispatcherInterface = $eventDispatcherInterface;
    }

    /**
     * Handles errors by generating a JSON response and potentially dispatching an error event.
     *
     * @param string $message The error message.
     * @param int $code The error code.
     * @param bool $msg_protect The error message protect (hide errors in prod env).
     * @return JsonResponse
     */
    public function handleError(string $message, int $code, bool $msg_protect = true): JsonResponse
    {
        // dispatch error event
        if ($this->canBeEventDispatched($message) && !$this->siteUtil->isMaintenance()) {
            $this->eventDispatcherInterface->dispatch(new ErrorEvent($code, 'internal-error', $message), ErrorEvent::NAME);
        }

        // protect error message in production env
        if ($_ENV['APP_ENV'] == 'prod' && $msg_protect == true) {
            $code = 500;
            $message = 'Unexpected server error';
        }

        // build error response
        $response = [
            'error' => [
                'status' => 'error',
                'code' => $code,
                'message' => $message
            ]
        ];

        return die(json_encode($response));
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
            'database connection error:',
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
