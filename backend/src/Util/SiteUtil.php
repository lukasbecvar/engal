<?php

namespace App\Util;

/**
 * Class SiteUtil
 * @package App\Util
 */
class SiteUtil
{
    /**
     * Gets the HTTP host.
     *
     * @return string The HTTP host.
     */
    public function getHttpHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Checks if the application is in maintenance mode.
     *
     * @return bool True if in maintenance mode, false otherwise.
     */
    public function isMaintenance(): bool 
    {
        // check if maintenance mode enabled in app enviroment
        if ($_ENV['MAINTENANCE_MODE'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the application is in dev mode.
     *
     * @return bool True if in dev mode, false otherwise.
     */
    public function isDevMode(): bool 
    {
        // check if dev mode enabled in app enviroment
        if ($_ENV['APP_ENV'] == 'dev') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if user registrations are enabled.
     *
     * @return bool True if user registrations are enabled, false otherwise.
     */
    public function isRegisterEnabled(): bool 
    {
        // check if registration enabled in app enviroment
        if ($_ENV['REGISTRATIONS'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if file storage enctyption is enabled.
     *
     * @return bool True if encryption are enabled, false otherwise.
     */
    public function isEncryptionEnabled(): bool 
    {
        // check if encryption enabled in app enviroment
        if ($_ENV['STORAGE_ENCRYPTION'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the application is running on localhost.
     *
     * @return bool True if running on localhost, false otherwise.
     */
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

    /**
     * Checks if the connection is over SSL.
     *
     * @return bool True if the connection is over SSL, false otherwise.
     */
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

    /**
     * Sends API headers for cross-origin resource sharing (CORS).
     */
    public function sendAPIHeaders(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            if (!headers_sent()) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST');
                header('Access-Control-Allow-Headers: X-Requested-With'); 
                header('Content-Type: application/json; charset=utf-8');
            }
        }
    }
}
