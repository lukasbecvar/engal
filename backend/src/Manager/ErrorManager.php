<?php

namespace App\Manager;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorManager
{
    public function handleError(string $message, int $code): void
    {
        // protect error message in production env
        if ($_ENV['APP_ENV'] == 'prod') {
            $code = 500;
            $message = 'Unexpected server error';
        }

        // build error response
        $response = new JsonResponse([
            'error' => [
                'status' => 'error',
                'code' => $code,
                'message' => $message
            ]
        ], $code);

        // send error response
        $response->send();

        // force die app
        die();
    }
}
