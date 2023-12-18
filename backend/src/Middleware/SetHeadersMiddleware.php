<?php

namespace App\Middleware;

use App\Util\SiteUtil;

/**
 * Class SetHeadersMiddleware
 * @package App\Middleware
 */
class SetHeadersMiddleware
{
    /**
     * @var SiteUtil $siteUtil The site utility.
     */ 
    private SiteUtil $siteUtil;

    /**
     * SetHeadersMiddleware constructor.
     * @param SiteUtil $siteUtil The site utility.
     */
    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

    /**
     * Sends API headers using the SiteUtil class when a kernel request is made.
     */
    public function onKernelRequest(): void
    {
        $this->siteUtil->sendAPIHeaders();
    }
}
