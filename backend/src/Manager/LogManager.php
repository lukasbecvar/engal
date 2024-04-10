<?php

namespace App\Manager;

use App\Entity\Log;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LogManager
 * 
 * The LogManager class handles logging of messages.
 * 
 * @package App\Manager
 */
class LogManager
{
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

    /**
     * LogManager constructor.
     *
     * @param ErrorManager $errorManager The error manager instance.
     * @param VisitorInfoUtil $visitorInfoUtil The visitor info utility instance.
     * @param EntityManagerInterface $entityManager The entity manager instance.
     */
    public function __construct(ErrorManager $errorManager, VisitorInfoUtil $visitorInfoUtil, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;        
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Logs a message with the given name and content.
     *
     * @param string $name The name of the log entry.
     * @param string $message The content of the log entry.
     * @return void
     */
    public function log(string $name, string $message): void
    {
        $log = new Log();

        // set log values
        $log->setName($name);
        $log->setMessage($message);
        $log->setTime(date('d.m.Y H:i:s'));
        $log->setIpAddress($this->visitorInfoUtil->getIP());
        $log->setStatus('non-readed');

        // try to save log to database
        try {
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError('log-error: '.$e->getMessage(), 500);
        }
    }
}
