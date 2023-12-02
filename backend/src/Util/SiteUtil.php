<?php

namespace App\Util;

class SiteUtil
{
    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function isMaintenance(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function isErrorMessagesAllowed(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['ERROR_MESSAGES'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function isDevMode(): bool 
    {
        // check if dev mode enabled in app enviroment
        if ($_ENV['APP_ENV'] == 'dev') {
            return true;
        } else {
            return false;
        }
    }

    public function isRegisterEnabled(): bool 
    {
        // check if dev mode enabled in app enviroment
        if ($_ENV['REGISTRATIONS'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function isRunningLocalhost(): bool 
    {
		$localhost = false;

        // get host url
        $host = $this->getHttpHost();

        // check if host is null
        if ($host != null) {

            // check if running on url localhost
            if (str_starts_with($host, 'localhost')) {
                $localhost = true;
            } 
                
            // check if running on localhost ip
            if (str_starts_with($host, '127.0.0.1')) {
                $localhost = true;
            }
            
            // check if running on private ip
            if (str_starts_with($host, '10.0.0.93')) {
                $localhost = true;
            }
        }

        return $localhost;
    }

    public function isSsl(): bool 
    {
        // check if set https header
        if (isset($_SERVER['HTTPS'])) {

            // https value (1)
            if ($_SERVER['HTTPS'] == 1) {
                return true;

            // check https value (on)
            } elseif ($_SERVER['HTTPS'] == 'on') {
                return true;
            } else {
                return false;   
            }
        } else {
            return false;   
        }
    }
}
