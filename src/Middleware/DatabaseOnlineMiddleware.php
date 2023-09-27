<?php

namespace App\Middleware;

use App\Helper\ErrorHelper;

/*
    This middleware used to check the availability of the database before allowing the visitor to visit the page
    if it fails to connect display error
*/

class DatabaseOnlineMiddleware
{
    
    private $doctrineConnection;
    private $errorHelper;

    public function __construct(\Doctrine\DBAL\Connection $doctrineConnection, ErrorHelper $errorHelper)
    {
        $this->doctrineConnection = $doctrineConnection;
        $this->errorHelper = $errorHelper;
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
