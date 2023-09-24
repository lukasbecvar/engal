<?php

namespace App\Helper;

use App\Entity\Log;
use App\Util\EscapeUtil;
use App\Util\VisitorInfoUtil;

/*
    Log helper provides log functions for save events to database table
*/

class LogHelper
{

    private $entityHelper;
    private $vistorInfoUtils;

    public function __construct(EntityHelper $entityHelper, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->entityHelper = $entityHelper;
        $this->vistorInfoUtils = $visitorInfoUtil;
    }

    // log action to database
    public function log(string $name, string $value): void 
    {
        if ($this->isLogsEnabled()) {

            // current date
            $date = date("d.m.Y H:i:s");

            // visitor browser agent
            $browser = VisitorInfoUtil::getBrowser();

            // visitor ip address
            $ipAddress = VisitorInfoUtil::getIP();

            // xss escape inputs
            $name = EscapeUtil::special_chars_strip($name);
            $value = EscapeUtil::special_chars_strip($value);
            $browser = EscapeUtil::special_chars_strip($browser);
            $ipAddress = EscapeUtil::special_chars_strip($ipAddress);
            
            // create new log enity
            $LogEntity = new Log();

            // set log entity values
            $LogEntity->setName($name); 
            $LogEntity->setValue($value); 
            $LogEntity->setDate($date); 
            $LogEntity->setRemoteAddr($ipAddress); 
            $LogEntity->setBrowser($browser); 
            $LogEntity->setStatus("unreaded"); 

            // insert new log to database
            $this->entityHelper->insertEntity($LogEntity);
        }
    }

    // check if log save enabled
    public function isLogsEnabled(): bool 
    {
        $state = false;

        // check if logs enabled
        if ($_ENV["LOGS_ENABLED"] == "true") {
            $state = true;
        }

        return $state;
    }
}
