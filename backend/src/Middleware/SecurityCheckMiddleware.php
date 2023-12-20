<?php

namespace App\Middleware;

use App\Util\SiteUtil;
use App\Manager\ErrorManager;

/**
 * Class SecurityCheckMiddleware
 * @package App\Middleware
 */
class SecurityCheckMiddleware
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
     * SecurityCheckMiddleware constructor.
     * @param SiteUtil $siteUtil The site utility.
     * @param ErrorManager $errorManager The error manager.
     */
    public function __construct(SiteUtil $siteUtil, ErrorManager $errorManager)
    {
        $this->siteUtil = $siteUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Performs security checks including SSL verification. If the application is not running on localhost,
     * it checks for SSL connection. If SSL is not present, it handles an error.
     */
    public function onKernelRequest(): void
    {
        // check if app not localhost running
        if (!$this->siteUtil->isRunningLocalhost()) {

            // check SSL
            if (!$this->siteUtil->isSsl()) {
                $this->errorManager->handleError('ssl-error: connection not running on ssl protocol', 500);
            }
        }
    }
}
