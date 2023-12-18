<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 * @package App\Util
 */
class VisitorInfoUtil
{
    /**
     * Gets the visitor's IP address.
     *
     * @return string|null The visitor's IP address, or null if not available.
     */
    public function getIP(): ?string 
    {
        // check client ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];

        // check forwarded ip (get ip from cloudflare visitors) 
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        
        // basic address get
        } else {
            $address = $_SERVER['REMOTE_ADDR'];
        }
        return $address;
    }

    /**
     * Gets the visitor's browser agent.
     *
     * @return string|null The visitor's browser agent, or 'Unknown' if not available.
     */
    public function getBrowser(): ?string 
    {
        // get user agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        // check if user agent found
        if ($user_agent != null) {
            return $user_agent;
        } else {
            return 'Unknown';
        }
    }
}
