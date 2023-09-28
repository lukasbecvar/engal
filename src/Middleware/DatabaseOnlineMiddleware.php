<?php

namespace App\Middleware;

use App\Helper\ErrorHelper;
use \Doctrine\DBAL\Connection as Connection;

/*
    This middleware used to check the availability of the database before allowing the visitor to visit the page
    if it fails to connect display error
*/

class DatabaseOnlineMiddleware
{
    
    private $errorHelper;
    private $doctrineConnection;

    public function __construct(
        ErrorHelper $errorHelper,
        Connection $doctrineConnection
    ) {
        $this->errorHelper = $errorHelper;
        $this->doctrineConnection = $doctrineConnection;
    }

    public function onKernelRequest(): void
    {
        try {
            // select for connection try
            $this->doctrineConnection->executeQuery("SELECT 1");
        } catch (\Exception $e) {

            // return error if not connected
            $this->errorHelper->handleError('database connection error: '.$e->getMessage(), 500);
        }
    }
}
