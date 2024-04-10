<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 * 
 * VisitorInfoUtil provides methods to get information about visitors.
 * 
 * @package App\Util
 */
class VisitorInfoUtil
{
    /**
     * Retrieves the IP address of the visitor.
     * 
     * @return string|null The IP address of the visitor, or null if it cannot be determined.
     */
    public function getIP(): ?string 
    {
        // check client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } 
        
        // check forwarded IP (get IP from cloudflare visitors) 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        
        // default addr get
        else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
