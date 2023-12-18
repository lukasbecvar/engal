<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;

/**
 * Class MaintenanceMiddleware
 * @package App\Middleware
 */
class MaintenanceMiddleware
{
    /**
     * @var SiteUtil $siteUtil The site utility.
     */
    private SiteUtil $siteUtil;

    /**
     * @var ErrorManager $errorManager The error manager.
     */
    private ErrorManager $errorManager;

    /**
     * MaintenanceMiddleware constructor.
     * @param SiteUtil $siteUtil The site utility.
     * @param ErrorManager $errorManager The error manager.
     */
    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager)
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Checks if the API is in maintenance mode. If maintenance mode is enabled, an error is handled.
     */
    public function onKernelRequest(): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($this->siteUtil->isMaintenance()) {
            $this->errorManager->handleError('Engal api is under maintenance mode, please try again later', 503, 'maintenance');
        }
    }
}
