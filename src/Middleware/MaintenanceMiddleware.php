<?php

namespace App\Middleware;

use App\Helper\ErrorHelper;

/*
    This middleware used to check if is application in devmode
*/

class MaintenanceMiddleware
{ 

    private $errorHelper;

    public function __construct(ErrorHelper $errorHelper)
    {
        $this->errorHelper = $errorHelper;
    }

    public function onKernelRequest(): void
    {
        // check if MAINTENANCE_MODE enabled
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {

            // handle maintenance page
            $this->errorHelper->handleErrorView('maintenance');            
        }
    }
}