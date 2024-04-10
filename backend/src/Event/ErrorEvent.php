<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ErrorEvent
 *
 * Represents an error event.
 * 
 * @package App\Event
 */
class ErrorEvent extends Event
{
    /**
     * The name of the event.
     */
    public const NAME = 'error.event';

    /**
     * @var int|null The error code.
     */
    protected ?int $error_code;

    /**
     * @var string|null The error name.
     */
    protected ?string $error_name;

    /**
     * @var string|null The error message.
     */
    protected ?string $error_message;

    /**
     * ErrorEvent constructor.
     *
     * @param int         $error_code    The error code.
     * @param string|null $error_name    The error name.
     * @param string|null $error_message The error message.
     */
    public function __construct(int $error_code, ?string $error_name, ?string $error_message)
    {
        $this->error_code = $error_code;
        $this->error_name = $error_name;
        $this->error_message = $error_message;
    }

    /**
     * Retrieves the error code.
     *
     * @return int|null The error code.
     */
    public function getErrorCode(): ?int
    {
        return $this->error_code;
    }

    /**
     * Retrieves the error name.
     *
     * @return string|null The error name.
     */
    public function getErrorName(): ?string
    {
        return $this->error_name;
    }

    /**
     * Retrieves the error message.
     *
     * @return string|null The error message.
     */
    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }
}
