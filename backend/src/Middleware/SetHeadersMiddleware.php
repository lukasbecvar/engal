<?php

namespace App\Middleware;

use App\Util\SiteUtil;

class SetHeadersMiddleware
{
    private SiteUtil $siteUtil;

    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

    public function onKernelRequest(): void
    {
        $this->siteUtil->sendAPIHeaders();
    }
}
