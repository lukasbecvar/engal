<?php

namespace App\Util;

class VisitorInfoUtil
{
    public function getIP(): ?string 
    {
        // check client ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $address = $_SERVER['HTTP_CLIENT_IP'];

        // check forwarded ip (get ip from cloudflare visitors) 
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {

            // basic address get
            $address = $_SERVER['REMOTE_ADDR'];
        }
        return $address;
    }

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
