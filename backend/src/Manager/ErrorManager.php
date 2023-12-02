<?php

namespace App\Manager;

class ErrorManager
{
    public function handleError(string $msg, int $code): void
    {
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
