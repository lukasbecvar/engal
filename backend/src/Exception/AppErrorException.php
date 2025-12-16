<?php

namespace AppException;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AppErrorException
 *
 * Custom exception class for application errors
 *
 * @package AppException
 */
class AppErrorException extends HttpException
{
    /**
     * AppErrorException constructor
     *
     * @param string|null     $message  The exception message
     * @param int             $code  The http error code
     * @param \Throwable|null $previous The previous throwable used for the exception chaining
     * @param array<mixed>    $headers  An array of HTTP headers. Each element should be a string representing a header
     */
    public function __construct(?string $message, int $code, ?\Throwable $previous = null, array $headers = [])
    {
        parent::__construct($code, $message, $previous, $headers);
    }
}
