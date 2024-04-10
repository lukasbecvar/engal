<?php

namespace App\Util;

/**
 * Class SiteUtil
 * 
 * SiteUtil provides basic site-related methods.
 * 
 * @package App\Util
 */
class SiteUtil
{
    /**
     * Check if the application is in maintenance mode.
     *
     * @return bool Whether the application is in maintenance mode.
     */
    public function isMaintenance(): bool
    {
        return $_ENV['MAINTENANCE_MODE'] === 'true';
    }
}
