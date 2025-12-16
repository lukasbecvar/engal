<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 *
 * VisitorInfoUtil provides methods to get information about visitors
 *
 * @package App\Util
 */
class VisitorInfoUtil
{
    /**
     * Retrieves the IP address of the visitor
     *
     * @return string|null The IP address of the visitor, or null if it cannot be determined
     */
    public function getIP(): ?string
    {
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            return '127.0.0.1';
        }

        // check client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check forwarded IP (get IP from cloudflare visitors)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        // default addr get
        return $_SERVER['REMOTE_ADDR'];
    }
}
