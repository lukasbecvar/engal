<?php

namespace App\Manager;

use App\Util\SiteUtil;

/**
 * Class ErrorManager
 * @package App\Manager
 */
class ErrorManager
{
    /**
     * @var SiteUtil $siteUtil The site utility.
     */
    private SiteUtil $siteUtil;

    /**
     * ErrorManager constructor.
     * @param SiteUtil $siteUtil The site utility.
     */
    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

    /**
     * Handles and logs errors, sending a JSON response with the error details.
     *
     * @param string $msg The error message.
     * @param int $code The HTTP status code.
     * @param string $status The status of the error (default is 'error').
     * @return void
     */
    public function handleError(string $msg, int $code, string $status = 'error'): void
    {
        // check if error messages is enabled (no for maintenance)
        if (!$this->siteUtil->isErrorMessagesAllowed() && !$this->siteUtil->isMaintenance()) {
            // replace error message (for protect exceptions)
            $msg = 'Unexpected server-side error, please try again later and report the error to your provider';
        } 

        // build error message
        $data = [
            'status' => $status,
            'code' => $code,
            'message' => $msg
        ];

        // send api headers
        $this->siteUtil->sendAPIHeaders();

        // JSON response
        die(json_encode($data));
    }  
}
