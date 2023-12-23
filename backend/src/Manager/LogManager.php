<?php 

namespace App\Manager;

use App\Entity\Log;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class LogManager
 * @package App\Manager
 */
class LogManager
{
    /**
     * @var ErrorManager $errorManager The error manager.
     */
    private ErrorManager $errorManager;
    
    /**
     * @var SecurityUtil $securityUtil The security utility.
     */
    private SecurityUtil $securityUtil;

    /**
     * @var VisitorInfoUtil $visitorInfoUtil The visitor information utility.
     */
    private VisitorInfoUtil $visitorInfoUtil;

    /**
     * @var EntityManagerInterface $entityManager The entity manager.
     */
    private EntityManagerInterface $entityManager;

    /**
     * LogManager constructor.
     * @param ErrorManager $errorManager The error manager.
     * @param SecurityUtil $securityUtil The security utility.
     * @param VisitorInfoUtil $visitorInfoUtil The visitor information utility.
     * @param EntityManagerInterface $entityManager The entity manager.
     */
    public function __construct(
        ErrorManager $errorManager,
        SecurityUtil $securityUtil, 
        VisitorInfoUtil $visitorInfoUtil, 
        EntityManagerInterface $entityManager
    ) {
        $this->errorManager = $errorManager;
        $this->securityUtil = $securityUtil;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Log a message with additional information.
     *
     * This method logs a message along with relevant information such as date and time,
     * visitor's browser agent, and IP address. The logged information is stored in the database.
     *
     * @param string $name The name or identifier associated with the logged message.
     * @param string $value The actual content or value of the logged message.
     *
     * @throws \Exception If an error occurs while attempting to save the log to the database.
     *
     * @return void
     */
    public function log(string $name, string $value): void 
    {
        // check if logs enabled in config
        if (!$this->isLogsEnabled()) {
            return;
        }

        // value string shortifiy
        if (mb_strlen($value) >= 100) {
            $value = mb_substr($value, 0, 100 - 3).'...';
        } 

        // get current date & time
        $time = date('d.m.Y H:i:s');

        // get visitor browser agent
        $browser = $this->visitorInfoUtil->getBrowser();

        // get visitor ip address
        $ip_address = $this->visitorInfoUtil->getIP();

        // xss inputs escape
        $name = $this->securityUtil->escapeString($name);
        $value = $this->securityUtil->escapeString($value);
        $browser = $this->securityUtil->escapeString($browser);
        $ip_address = $this->securityUtil->escapeString($ip_address);
                
        // create new log object
        $LogEntity = new Log();

        // set log object values
        $LogEntity->setName($name); 
        $LogEntity->setValue($value); 
        $LogEntity->setBrowser($browser); 
        $LogEntity->setTime($time); 
        $LogEntity->setUserIp($ip_address); 
        $LogEntity->setStatus('unreaded'); 
                
        // try insert row
        try {
            $this->entityManager->persist($LogEntity);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->errorManager->handleError('log save error: '.$e->getMessage(), 500);  
        }
    }

    /**
     * Checks if logs are enabled.
     *
     * @return bool Returns true if logs are enabled, otherwise false.
     */
    public function isLogsEnabled(): bool 
    {
        // check if logs enabled
        if ($_ENV['LOGS_ENABLED'] == 'true') {
            return true;
        } else {
            return false;
        }
    }
}
