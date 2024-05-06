<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class MaintenanceMiddleware
{
    private SiteUtil $siteUtil;

    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

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
