<?php

namespace App\Util;

class SiteUtil
{
    public function isMaintenance(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }
}
