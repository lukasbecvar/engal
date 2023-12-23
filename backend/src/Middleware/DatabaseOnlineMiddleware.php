<?php

namespace App\Middleware;

use App\Manager\ErrorManager;
use \Doctrine\DBAL\Connection as Connection;

/**
 * Class DatabaseOnlineMiddleware
 * @package App\Middleware
 */
class DatabaseOnlineMiddleware
{
    /**
     * @var ErrorManager $errorManager The error manager.
     */
    private ErrorManager $errorManager;

    /**
     * @var Connection $doctrineConnection The Doctrine database connection.
     */
    private Connection $doctrineConnection;

    /**
     * DatabaseOnlineMiddleware constructor.
     * @param ErrorManager $errorManager The error manager.
     * @param Connection $doctrineConnection The Doctrine database connection.
     */
    public function __construct(ErrorManager $errorManager, Connection $doctrineConnection) 
    {
        $this->errorManager = $errorManager;
        $this->doctrineConnection = $doctrineConnection;
    }

    /**
     * Event listener method triggered on each kernel request to check the database connection.
     *
     * This method attempts to execute a simple query to check the database connection.
     * If the database is not connected, it handles the error using the error manager.
     *
     * @return void
     *
     * @throws \Exception If an error occurs during the process of checking the database connection.
     */
    public function onKernelRequest(): void
    {
        try {
            // select for connection try
            $this->doctrineConnection->executeQuery('SELECT 1');
        } catch (\Exception $e) {

            // handle error if database not connected
            $this->errorManager->handleError('database connection error: '.$e->getMessage(), 503);
        }
    }
}
