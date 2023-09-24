<?php

namespace App\Helper;

/*
    Site helper provides main site get, set methods
*/

class SiteHelper
{
    
    public function getHttpHost(): ?string
    {
        return $_SERVER['HTTP_HOST'];
    }

    // check if site running on localhost
    public function isRunningLocalhost(): bool 
    {
		$state = false;

        // get http host
        $host = $this->getHttpHost();
            
        // check if running on url localhost
        if (str_starts_with($host, "localhost")) {
            $state = true;
        } 
            
        // check if running on localhost ip
        if (str_starts_with($host, "127.0.0.1")) {
            $state = true;
        }
        return $state;
    }
}
