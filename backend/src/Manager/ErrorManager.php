<?php

namespace App\Manager;

use App\Util\SiteUtil;

class ErrorManager
{
    private SiteUtil $siteUtil;

    public function __construct(SiteUtil $siteUtil)
    {
        $this->siteUtil = $siteUtil;
    }

    public function handleError(string $msg, int $code): void
    {
        // check if error messages is enabled (no for maintenance)
        if (!$this->siteUtil->isErrorMessagesAllowed() && !$this->siteUtil->isMaintenance()) {
            // replace error message (for protect exceptions)
            $msg = 'Unexpected server-side error, please try again later and report the error to your provider';
        } 

        // build error message
        $data = [
            'status' => 'error',
            'code' => $code,
            'message' => $msg
        ];

        // JSON response
        die(json_encode($data));
    }  
}
