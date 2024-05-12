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
    protected ?int $errorCode;

    /**
     * @var string|null The error name.
     */
    protected ?string $errorName;

    /**
     * @var string|null The error message.
     */
    protected ?string $errorMessage;

    /**
     * ErrorEvent constructor.
     *
     * @param int         $errorCode    The error code.
     * @param string|null $errorName    The error name.
     * @param string|null $errorMessage The error message.
     */
    public function __construct(int $errorCode, ?string $errorName, ?string $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorName = $errorName;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Retrieves the error code.
     *
     * @return int|null The error code.
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Retrieves the error name.
     *
     * @return string|null The error name.
     */
    public function getErrorName(): ?string
    {
        return $this->errorName;
    }

    /**
     * Retrieves the error message.
     *
     * @return string|null The error message.
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
