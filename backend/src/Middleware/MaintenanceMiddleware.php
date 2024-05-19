<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class MaintenanceMiddleware
 *
 * This middleware is used to check if the application is in maintenance mode.
 *
 * @package App\Service\Middleware
 */
class MaintenanceMiddleware
{
    private SiteUtil $siteUtil;

    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

    /**
     * Check if the application is in maintenance mode.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $arr = [
                "status" => "error",
                "code" => 520,
                "message" => "Engal API is in maintenance mode"
            ];

            // create JSON response
            $response = new JsonResponse($arr);

            // set response
            $event->setResponse($response);
        }
    }
}
