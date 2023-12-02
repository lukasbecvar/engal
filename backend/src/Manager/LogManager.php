<?php 

namespace App\Manager;

use App\Entity\Log;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;

class LogManager
{
    private ErrorManager $errorManager;
    private SecurityUtil $securityUtil;
    private VisitorInfoUtil $visitorInfoUtil;
    private EntityManagerInterface $entityManager;

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

    public function log(string $name, string $value): void 
    {
        // check if logs enabled in config
        if ($this->isLogsEnabled()) {

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
                $this->errorManager->handleError('log flush error: '.$e->getMessage(), 500);  
            }
        }
    }

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
